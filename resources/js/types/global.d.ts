import { PageProps as InertiaPageProps } from '@inertiajs/core';
import { AxiosInstance } from 'axios';
import ziggyRoute from 'ziggy-js';
import { PageProps as AppPageProps } from './';

declare global {
  interface Window {
    axios: AxiosInstance;
  }

  // eslint-disable-next-line no-var
  var route: typeof ziggyRoute;
}

declare module 'vue' {
  interface ComponentCustomProperties {
    route: typeof ziggyRoute;
  }
}

declare module '@inertiajs/core' {
  interface PageProps extends InertiaPageProps, AppPageProps {}
}
