import React from "react"
import { createRoot } from 'react-dom/client';
import { Provider } from "react-redux"
import store from "@singleItemButton/store"
import "@singleItemButton/assets/css/main.scss"
import App from "@singleItemButton/App"

// Function to render React components
const singleItemButton = () => {

  const rootElement = document.getElementById('olena-food-ordering-single-item-button');

  const postId = rootElement?.dataset?.postId;
  
  if (postId) {
    window.vajofoSingleItemButtonLocalizer = {
      ...window.vajofoSingleItemButtonLocalizer,
      postId
    };
  }

  if (!rootElement) return;

  const root = createRoot(rootElement);

  root.render(
    <React.StrictMode>
      <Provider store={store}>
        <App />
      </Provider>
    </React.StrictMode>
  );
}

// Call the render function when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', singleItemButton)