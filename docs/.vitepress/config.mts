import { defineConfig } from "vitepress";

export default defineConfig({
  title: "Helix",
  description: "Vector similarity search engine and dashboard for Laravel.",
  lang: "en-US",
  lastUpdated: true,
  markdown: {
    theme: {
      light: "catppuccin-latte",
      dark: "catppuccin-mocha",
    },
  },
  themeConfig: {
    nav: [
      { text: "Getting Started", link: "/getting-started/introduction" },
      { text: "Usage", link: "/usage/quick-start" },
      { text: "Examples", link: "/examples/rag" },
    ],
    sidebar: [
      {
        text: "Getting Started",
        items: [
          { text: "Introduction", link: "/getting-started/introduction" },
          { text: "Installation", link: "/getting-started/installation" },
          { text: "Configuration", link: "/getting-started/configuration" },
        ],
      },
      {
        text: "Usage",
        items: [
          { text: "Quick Start", link: "/usage/quick-start" },
          { text: "Search", link: "/usage/search" },
          { text: "Recommendations", link: "/usage/recommendations" },
          { text: "Snapshots", link: "/usage/snapshots" },
          { text: "Dashboard", link: "/usage/dashboard" },
          { text: "API Reference", link: "/usage/api" },
        ],
      },
      {
        text: "Examples",
        items: [
          { text: "RAG ", link: "/examples/rag" },
          { text: "Semantic Search", link: "/examples/semantic-search" },
          { text: "Recommendation System", link: "/examples/recommendations" },
        ],
      },
    ],
    socialLinks: [
      { icon: "github", link: "https://github.com/mrfelipemartins/helix" },
    ],
    footer: {
      message: "Released under the MIT License.",
      copyright: "Copyright © 2026 MrFelipeMartins",
    },
  },
});
