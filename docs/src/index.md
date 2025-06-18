---
# https://vitepress.dev/reference/default-theme-home-page
layout: home

hero:
  name: "Code Generator"
  text: "Laravel package for Faster API Development."
  tagline: "Less Typing. More Building."
  image: 
    src: /hero.svg
    alt: Laravel Code Generator Hero
  actions:
    - theme: brand
      text: Get Started
      link: introduction/why-code-generator
    - theme: alt
      text: GitHub
      link: https://github.com/7span/laravel-code-generator

features:
  - icon: 🚀
    title: Generate REST API Files
    details: Quickly generate Models, Migrations, Controllers, Services, Resources, Requests, Policies, Notifications, Traits, and more based on user-defined input.

  - icon: 🎨
    title: Interactive UI
    details: An interactive, dynamic interface to visually define models, fields, relationships, and code generation options
    
  - icon: 🛠
    title: Customizable Paths
    details: Customize folder paths, namespaces, route prefixes, and stub templates to match your application's architecture.
  - icon: 📜
    title: View Generated Files Logs
    details:  View package logs directly from the UI for troubleshooting and transparency.
---


<script setup>
import { VPTeamMembers } from 'vitepress/theme'

const members = [
  {
    avatar: 'https://github.com/7span.png',
    name: '7Span',
    title: 'Sponsor',
    links: [
      { icon: 'github', link: 'https://github.com/7span' },
      { icon: 'x', link: 'https://x.com/7SpanHQ' }
    ]
  },
  {
    avatar: 'https://github.com/hemratna.png',
    name: 'Hemratna Bhimani',
    title: 'Creator',
    links: [
      { icon: 'github', link: 'https://github.com/hemratna' },
    ]
  },
  {
    avatar: 'https://github.com/kajal-7span.png',
    name: 'Kajal Pandya',
    title: 'Contributor',
    links: [
      { icon: 'github', link: 'https://github.com/kajal-7span' },
    ]
  },
  {
    avatar: 'https://avatars.githubusercontent.com/u/205601895?v=4',
    name: 'Dhaval Rajput',
    title: 'Contributor',
    links: [
      { icon: 'github', link: 'https://github.com/dhaval-j-r-7span' },
    ]
  },
  {
    avatar: 'https://avatars.githubusercontent.com/u/205601566?v=4',
    name: 'Mruganshi Chodavadiya',
    title: 'Contributor',
    links: [
      { icon: 'github', link: 'https://github.com/mruganshi-7span' },
    ]
  },
  {
    avatar: 'https://avatars.githubusercontent.com/u/92076426?s=64&v=4',
    name: 'Nikunj Gadhiya',
    title: 'Contributor',
    links: [
      { icon: 'github', link: 'https://github.com/nikunj-7span' },
    ]
  },
  {
    avatar: 'https://avatars.githubusercontent.com/u/109651349?s=64&v=4',
    name: 'Ujas Patel',
    title: 'Contributor',
    links: [
      { icon: 'github', link: 'https://github.com/ujas-7span' },
    ]
  },
]
</script>

### 🙌 Credits

> A big thank you to the amazing contributors who made **Laravel Code Generator** possible. Whether it’s designing, coding, or maintaining — your work powers this tool. 💪

<VPTeamMembers size="small" :members />
