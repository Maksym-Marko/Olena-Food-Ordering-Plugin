import React from "react"
import { createRoot } from 'react-dom/client';
import "@cartWidget/assets/css/main.scss"
import App from "@cartWidget/App"

// Function to render React components
const cartWidget = () => {

  const rootElement = document.getElementById('olena-food-ordering-cart-widget');

  if (!rootElement) return;

  const root = createRoot(rootElement);

  root.render(
    <React.StrictMode>
      <App />
    </React.StrictMode>
  );
}

// Call the render function when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', cartWidget)