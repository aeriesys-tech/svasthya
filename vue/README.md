# mental-wellness-app

This template should help get you started developing with Vue 3 in Vite.

## Recommended IDE Setup

[VSCode](https://code.visualstudio.com/) + [Volar](https://marketplace.visualstudio.com/items?itemName=Vue.volar) (and disable Vetur).

## Customize configuration

See [Vite Configuration Reference](https://vite.dev/config/).

## Project Setup

```sh
npm install
```

### Compile and Hot-Reload for Development

```sh
npm run dev
```

### Compile and Minify for Production

```sh
npm run build
```

## 🚀 How Vue 3 Works in This Project

Vue 3 is a progressive JavaScript framework for building user interfaces.  
This project uses **Vite** as the build tool for faster development and optimized production builds.

### 🔑 Key Vue 3 Concepts Used Here

- **Single File Components (SFCs)**  
  Each component lives in a `.vue` file with three sections:  
  - `<template>` → Defines the HTML markup.  
  - `<script setup>` → Contains component logic using Composition API.  
  - `<style>` → Scoped or global CSS for styling.  

- **Reactive State Management**  
  Vue’s reactivity system automatically updates the UI when reactive variables (`ref`, `reactive`) change.  

- **Props and Events**  
  - Data is passed **down** to child components using `props`.  
  - Events are emitted **up** using `emit`.  

- **Routing** (if Vue Router is enabled)  
  Navigation between pages happens without reloading the browser using **Vue Router**.  

- **API Communication**  
  The frontend communicates with the **backend** (or any API) using `fetch` or `axios`.  

- **Hot Module Replacement (HMR)**  
  When you edit `.vue` files, the changes are applied instantly in the browser without a full reload.  

### Folder Structure
mental-wellness-app/
|──public/
|   ├── images/        # Static images like logo and user icons etc,
│   ├── sounds/        # Audio for notification alert
│   ├── theme/         # All theme related codes (css,fonts,images,js)
│── src/
│   ├── assets/        # Static assets (images, icons, styles)
│   ├── components/    # Vue components (UI building blocks) use for code reusability
│   ├── views/         # Page-level components (Can create folders for each module/feature)
│   ├── router/        # Vue Router configuration (to navigate between pages)
│   ├── store/         # Pinia/Vuex store (if used for state management)
│   ├── App.vue        # Root component
│   └── main.js        # Application entry point
│
│── index.html         # Main HTML template
│── package.json       # Dependencies and scripts
│── vite.config.js     # Vite configuration
|── .env               # Used to store secrets and urls
