// vite.config.js
export default defineConfig({
  css: {
    transformer: "lightningcss",
  },
  build: {
    cssMinify: "lightningcss",
    // ...
  },
});
