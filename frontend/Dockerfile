FROM node:25-alpine3.22

RUN apk add --no-cache tzdata
ENV TZ=America/Sao_Paulo

RUN apk add --no-cache libc6-compat

WORKDIR /app

ENV HOST=0.0.0.0
ENV PORT=3000

CMD ["npm", "run", "dev"]