// .github/scripts/generate-pr-description.js
// Uses GitHub Models API (free, no extra secrets needed — reuses GITHUB_TOKEN)
// Model: gpt-4o-mini — good balance of quality and speed for PR descriptions

const https = require("https");

// ─── Helpers ────────────────────────────────────────────────────────────────

/**
 * Simple HTTPS request wrapper returning parsed JSON.
 * No external dependencies — runs with zero npm install.
 */
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

// ─── Filters ────────────────────────────────────────────────────────────────

/**
 * Files that add noise without meaningful context for the AI.
 * Covers Laravel 12 + Nuxt 4 standard generated/lock files.
 */
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

  // Documentation (as requested)
  /^docs\//,

  // Build artifacts & IDE
  /^dist\//,
  /\.min\.(js|css)$/,
  /\.map$/,
  /^\.idea\//,
  /^\.vscode\//,
];

/**
 * Returns true if the file path should be excluded from the diff.
 */
function shouldIgnore(filePath) {
  return IGNORED_PATTERNS.some((pattern) => pattern.test(filePath));
}

/**
 * Parses a unified diff string and returns only the hunks
 * for files that are NOT in the ignore list.
 */
function filterDiff(rawDiff) {
  const fileBlocks = rawDiff.split(/(?=^diff --git)/m);

  const filtered = fileBlocks.filter((block) => {
    const match = block.match(/^diff --git a\/(.+?) b\//m);
    if (!match) return false;
    return !shouldIgnore(match[1]);
  });

  return filtered.join("\n");
}

// ─── GitHub API ─────────────────────────────────────────────────────────────

/**
 * Fetches the raw unified diff for the pull request.
 */
async function getPrDiff(owner, repo, prNumber, token) {
  return new Promise((resolve, reject) => {
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
}

/**
 * Fetches commit messages for the PR to give extra context to the AI.
 */
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

/**
 * Updates the PR body via GitHub REST API.
 */
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

// ─── GitHub Models API ──────────────────────────────────────────────────────

/**
 * Calls GitHub Models (gpt-4o-mini) using the OpenAI-compatible endpoint.
 * Authentication reuses the GITHUB_TOKEN — no extra secrets needed.
 */
async function callGitHubModels(token, diff, commits, template) {
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
    "https://models.inference.ai.azure.com/chat/completions",
    {
      method: "POST",
      headers: {
        // GitHub Models uses the same GITHUB_TOKEN — no new secret needed
        Authorization: `Bearer ${token}`,
        "Content-Type": "application/json",
      },
    },
    {
      model: "gpt-4o-mini", // fast, free, great for structured text generation
      messages: [
        {
          role: "system",
          content:
            "You are a senior developer that writes clear and objective Pull Request descriptions in Brazilian Portuguese.",
        },
        {
          role: "user",
          content: prompt,
        },
      ],
      temperature: 0.3, // low = more deterministic, less hallucination
      max_tokens: 2048,
    }
  );

  if (status !== 200) {
    throw new Error(
      `GitHub Models API error. Status: ${status} — ${JSON.stringify(body)}`
    );
  }

  // OpenAI-compatible response format
  return body.choices[0].message.content;
}

// ─── Main ────────────────────────────────────────────────────────────────────

async function main() {
  const {
    GITHUB_TOKEN,
    GITHUB_REPOSITORY,
    PR_NUMBER,
    PR_TEMPLATE,
  } = process.env;

  const [owner, repo] = GITHUB_REPOSITORY.split("/");

  console.log(`Processing PR #${PR_NUMBER} on ${owner}/${repo}`);

  // 1. Fetch raw diff and commits
  console.log("Fetching diff and commits...");
  const rawDiff = await getPrDiff(owner, repo, PR_NUMBER, GITHUB_TOKEN);
  const commits = await getPrCommits(owner, repo, PR_NUMBER, GITHUB_TOKEN);

  // 2. Filter noise from diff
  const filteredDiff = filterDiff(rawDiff);
  console.log(`Diff size after filtering: ${filteredDiff.length} characters`);

  if (filteredDiff.trim().length === 0) {
    console.log("No meaningful diff found after filtering. Skipping.");
    process.exit(0);
  }

  // 3. Call GitHub Models
  console.log("Calling GitHub Models API...");
  const description = await callGitHubModels(
    GITHUB_TOKEN,
    filteredDiff,
    commits,
    PR_TEMPLATE
  );

  // 4. Update the PR
  console.log("Updating PR description...");
  await updatePrDescription(owner, repo, PR_NUMBER, GITHUB_TOKEN, description);

  console.log("Done! PR description updated successfully.");
}

main().catch((err) => {
  console.error("Error:", err.message);
  process.exit(1);
});