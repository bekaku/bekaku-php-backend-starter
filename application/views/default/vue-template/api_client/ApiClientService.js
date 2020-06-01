import Service from './Service';

class ApiClientService extends Service {
    constructor() {
        super();
    }
    async get(pageParam) {
        return this.callApiGet(`/apiClient${pageParam}`);
    }
    async create(postData) {
        return this.callApiPost(`/apiClient`, postData);
    }
    async update(postData) {
        return this.callApiPut(`/apiClient`, postData);
    }
    async delete(id) {
        return this.callApiDelete(`/apiClient?_ids=${id}`);
    }
}
export default ApiClientService
/* route.js
            {
              path: "api-client",
              name: "app-api-client",
              component: () => import("@/views/App/pages/app/ApiClient"),
              meta: {
                breadcrumb: [
                  { text: "nav.dashboard", href: "/", disabled: false },
                  { text: "model.api_client.api_client", href: "", disabled: true }
                ],
                pageTitle: { text: "nav.api_client.api_client"}
              }
            },
*/

/* UseMenuApi.js
        {
          title: "model.api_client.api_client",
          icon: "mdi-file-outline",
          to: "/app/api-client"
         }
*/
