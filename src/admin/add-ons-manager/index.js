import React from "react"
import { createRoot } from 'react-dom/client';
import { RouterProvider } from "react-router-dom"
import router from "@addOnsManager/router"
import store from "@addOnsManager/store"
import { Provider } from "react-redux"
import "@addOnsManager/assets/css/main.scss"

// Function to render React components
const renderAddOnsWrapper = () => {

  const rootElement = document.getElementById('olena-food-ordering-add-ons-wrapper');

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

document.addEventListener('DOMContentLoaded', renderAddOnsWrapper)