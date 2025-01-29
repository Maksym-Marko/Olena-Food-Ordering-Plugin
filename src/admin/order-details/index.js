import React from "react"
import { createRoot } from 'react-dom/client';
import { RouterProvider } from "react-router-dom"
import router from "@orderDetails/router"
import store from "@orderDetails/store"
import { Provider } from "react-redux"
import "@orderDetails/assets/css/main.scss"

// Function to render React components
const renderOrderDetailsPage = () => {

  const rootElement = document.getElementById('olena-food-ordering-order-data-wrapper');

  if(!rootElement) return;

  const root = createRoot(rootElement);

  root.render(
    <React.StrictMode>
      <Provider store={store}>
        <RouterProvider router={router} />
      </Provider>
    </React.StrictMode>
  );
}

// Call the render function when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', renderOrderDetailsPage)