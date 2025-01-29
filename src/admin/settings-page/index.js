import React from "react"
import { createRoot } from 'react-dom/client';
import { RouterProvider } from "react-router-dom"
import router from "@settings/router"
import store from "@settings/store"
import { Provider } from "react-redux"
import "@settings/assets/css/main.scss"

// Function to render React components
const renderSettingsPage = () => {

  // event 'ofo_settings_event'
  // document.addEventListener('ofo_settings_event', event => console.log(event.detail))

  // document.dispatchEvent('new Event('ofo_settings_event'))

  // document.dispatchEvent('new CustomEvent('ofo_settings_event', {detail: 'example}))

  // let config = { foo: 'bar', example: () => {} }

  // 

  const rootElement = document.getElementById('olena-food-ordering-settings');

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
document.addEventListener('DOMContentLoaded', renderSettingsPage)