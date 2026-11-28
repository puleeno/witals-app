import { defineConfig } from 'vite';
import solid from 'vite-plugin-solid';

// Builds the admin SPA as a single IIFE bundle consumed by the legacy
// AdminController chrome (public/js/admin-solid-core.js). CSS is authored as
// static design tokens in public/css so it needs no transform step.
export default defineConfig({
  plugins: [solid()],
  publicDir: false,
  build: {
    outDir: '../public/js',
    emptyOutDir: false,
    target: 'es2020',
    lib: {
      entry: 'src/main.jsx',
      formats: ['iife'],
      name: 'AdminSolidCore',
      fileName: () => 'admin-solid-core.js',
    },
    rollupOptions: {
      output: { inlineDynamicImports: true },
    },
  },
});