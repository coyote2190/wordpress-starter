import { defineConfig } from "vite";

export default defineConfig({
  build: {
    outDir: "assets/dist",
    emptyOutDir: true,
    rollupOptions: {
      input: {
        main: "assets/src/js/main.js",
      },
      output: {
        entryFileNames: "main.js",
        chunkFileNames: "chunks/[name].js",
        assetFileNames: (assetInfo) => {
          if (assetInfo.name.endsWith(".css")) return "main.css";
          return "assets/[name][extname]";
        },
      },
    },
  },
});
