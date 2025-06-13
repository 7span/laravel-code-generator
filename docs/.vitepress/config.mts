import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: "Code Generator",
  description: "Your Files Generator Companion",
  srcDir: "src",
  base: "/open-source/code-generator",
  themeConfig: {
    // https://vitepress.dev/reference/default-theme-config
    nav: [
      { text: 'Home', link: '/' },
      { text: 'User Guide', link: '/introduction/why-code-generator' }
    ],

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
                    { text: "Generated Files", link: "/usage/files" },
                    { text: "Logs", link: "/usage/logs" },
                ],
            },
        ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/7span/laravel-code-generator' }
    ]
  }
})
