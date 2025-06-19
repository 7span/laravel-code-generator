import { defineConfig } from "vitepress";

export default defineConfig({
  title: "Code Generator",
  description: "Your Files Generator Companion",
  srcDir: "src",
  head: [["link", { rel: "icon", type: "image/svg+xml", href: "/logo.svg" }]],
  themeConfig: {
    logo: "/logo.svg",
    nav: [{ text: "Home", link: "/" }],

    sidebar: [
      {
        text: "Getting Started",
        items: [
          {
            text: "Why Laravel Code Generator?",
            link: "/introduction/why-code-generator",
          },
          {
            text: "Installation",
            link: "/introduction/installation",
          },
          {
            text: "Configuration",
            link: "/introduction/configuration",
          },
        ],
      },
      {
        text: "Usage",
        items: [
          { text: "Generate Files", link: "/usage/files" },
          { text: "Logs", link: "/usage/logs" },
        ],
      },
    ],

    socialLinks: [
      {
        icon: "github",
        link: "https://github.com/7span/laravel-code-generator",
      },
    ],
    search: {
      provider: "local",
    },
  },
});
