// Esse script engloba as diff da PR, filtra ruidos, chama o Gemini e atualiza a descrição da PR
const https = require("https");

function request(url, options = {}, body = null) {
  return new Promise((resolve, reject) => {
    const req = https.request(url, options, (res) => {
      let data = "";
      res.on("data", (chunk) => (data += chunk));
      res.on("end", () => {
        try {
          resolve({ status: res.statusCode, body: JSON.parse(data) });
        } catch {
          resolve({ status: res.statusCode, body: data });
        }
      });
    });

    req.on("error", reject);
    if (body) req.write(typeof body === "string" ? body : JSON.stringify(body));
    req.end();
  });
}

const IGNORED_PATTERNS = [
  // Dependency lock files
  /package-lock\.json$/,
  /composer\.lock$/,
  /yarn\.lock$/,
  /pnpm-lock\.yaml$/,

  // Installed dependencies
  /^vendor\//,
  /^node_modules\//,

  // Laravel generated / cache
  /^storage\//,
  /^bootstrap\/cache\//,
  /\.env/,

  // Nuxt generated
  /^\.nuxt\//,
  /^\.output\//,
  /^public\/build\//,

  // Documentation
  /^docs\//,

  // Build artifacts & IDE
  /^dist\//,
  /\.min\.(js|css)$/,
  /\.map$/,
  /^\.idea\//,
  /^\.vscode\//,
];

function shouldIgnore(filePath) {
  return IGNORED_PATTERNS.some((pattern) => pattern.test(filePath));
}

function filterDiff(rawDiff) {
  const fileBlocks = rawDiff.split(/(?=^diff --git)/m);

  const filtered = fileBlocks.filter((block) => {
    // Extract file path from "diff --git a/path b/path"
    const match = block.match(/^diff --git a\/(.+?) b\//m);
    if (!match) return false;
    return !shouldIgnore(match[1]);
  });

  return filtered.join("\n");
}

async function getPrDiff(owner, repo, prNumber, token) {
  const response = await new Promise((resolve, reject) => {
    const options = {
      hostname: "api.github.com",
      path: `/repos/${owner}/${repo}/pulls/${prNumber}`,
      headers: {
        Accept: "application/vnd.github.v3.diff",
        Authorization: `Bearer ${token}`,
        "User-Agent": "pr-description-bot",
      },
    };

    https.get(options, (res) => {
      let data = "";
      res.on("data", (chunk) => (data += chunk));
      res.on("end", () => resolve(data));
      res.on("error", reject);
    });
  });

  return response;
}

async function getPrCommits(owner, repo, prNumber, token) {
  const { body } = await request(
    `https://api.github.com/repos/${owner}/${repo}/pulls/${prNumber}/commits`,
    {
      headers: {
        Authorization: `Bearer ${token}`,
        "User-Agent": "pr-description-bot",
        Accept: "application/vnd.github.v3+json",
      },
    }
  );

  return body.map((c) => `- ${c.commit.message.split("\n")[0]}`).join("\n");
}

async function updatePrDescription(owner, repo, prNumber, token, body) {
  const { status } = await request(
    `https://api.github.com/repos/${owner}/${repo}/pulls/${prNumber}`,
    {
      method: "PATCH",
      headers: {
        Authorization: `Bearer ${token}`,
        "User-Agent": "pr-description-bot",
        Accept: "application/vnd.github.v3+json",
        "Content-Type": "application/json",
      },
    },
    { body }
  );

  if (status !== 200) {
    throw new Error(`Failed to update PR description. Status: ${status}`);
  }
}

async function callGemini(apiKey, diff, commits, template) {
  const prompt = `
You are a senior developer writing a Pull Request description in Brazilian Portuguese.
Your goal is to fill the PR template below based ONLY on the provided diff and commit messages.

Rules:
- Write in Brazilian Portuguese
- Be objective and technical
- For the checkboxes, mark ONLY those you are confident about based on the diff
- For the Jira field, keep "VCE-XXX" as-is (you have no ticket context)
- For "Como testar", infer logical steps from what was changed
- Do NOT invent information not present in the diff
- Return ONLY the filled template, no explanations, no markdown code fences

──────────────────────────────
COMMIT MESSAGES:
${commits}

──────────────────────────────
CODE DIFF (filtered):
${diff}

──────────────────────────────
TEMPLATE TO FILL:
${template}
`.trim();

  const { status, body } = await request(
    `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=${apiKey}`,
    {
      method: "POST",
      headers: { "Content-Type": "application/json" },
    },
    {
      contents: [{ parts: [{ text: prompt }] }],
      generationConfig: {
        temperature: 0.3, 
        maxOutputTokens: 2048,
      },
    }
  );

  if (status !== 200) {
    throw new Error(`Gemini API error. Status: ${status} — ${JSON.stringify(body)}`);
  }

  return body.candidates[0].content.parts[0].text;
}

async function main() {
  const {
    GITHUB_TOKEN,
    GEMINI_API_KEY,
    GITHUB_REPOSITORY, 
    PR_NUMBER,
    PR_TEMPLATE,
  } = process.env;

  const [owner, repo] = GITHUB_REPOSITORY.split("/");

  console.log(`Processing PR #${PR_NUMBER} on ${owner}/${repo}`);

  console.log("Fetching diff and commits...");
  const rawDiff = await getPrDiff(owner, repo, PR_NUMBER, GITHUB_TOKEN);
  const commits = await getPrCommits(owner, repo, PR_NUMBER, GITHUB_TOKEN);

  const filteredDiff = filterDiff(rawDiff);
  console.log(`Diff size after filtering: ${filteredDiff.length} characters`);

  if (filteredDiff.trim().length === 0) {
    console.log("No meaningful diff found after filtering. Skipping.");
    process.exit(0);
  }

  console.log("Calling Gemini API...");
  const description = await callGemini(GEMINI_API_KEY, filteredDiff, commits, PR_TEMPLATE);

  console.log("Updating PR description...");
  await updatePrDescription(owner, repo, PR_NUMBER, GITHUB_TOKEN, description);

  console.log("Done! PR description updated successfully.");
}

main().catch((err) => {
  console.error("Error:", err.message);
  process.exit(1);
});