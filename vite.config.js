import { defineConfig } from "vite";
import fs from "node:fs";
import path from "node:path";

const DEV_PORT = 5173;
const HOT_FILE = path.resolve("assets/dist/.vite-hot");

function wpHotFile() {
  return {
    name: "wp-hot-file",
    configureServer() {
      fs.mkdirSync(path.dirname(HOT_FILE), { recursive: true });
      fs.writeFileSync(HOT_FILE, `http://localhost:${DEV_PORT}`);

      const cleanup = () => {
        if (fs.existsSync(HOT_FILE)) fs.unlinkSync(HOT_FILE);
      };
      process.on("exit", cleanup);
      process.on("SIGINT", () => {
        cleanup();
        process.exit();
      });
      process.on("SIGTERM", () => {
        cleanup();
        process.exit();
      });
    },
  };
}

export default defineConfig({
  plugins: [wpHotFile()],
  server: {
    host: "localhost",
    port: DEV_PORT,
    strictPort: true,
    cors: true,
    origin: `http://localhost:${DEV_PORT}`,
  },
  build: {
    outDir: "assets/dist",
    emptyOutDir: true,
    rollupOptions: {
      input: { main: "assets/src/js/main.js" },
      output: {
        entryFileNames: "main.js",
        chunkFileNames: "chunks/[name].js",
        assetFileNames: (assetInfo) => {
          const name = assetInfo.names?.[0] ?? assetInfo.name ?? "";
          if (name.endsWith(".css")) return "main.css";
          return "assets/[name][extname]";
        },
      },
    },
  },
});
