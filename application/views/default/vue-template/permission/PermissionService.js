import Service from './Service';

class PermissionService extends Service {
    constructor() {
        super();
    }
    async get(pageParam) {
        return this.callApiGet(`/permission${pageParam}`);
    }
    async create(postData) {
        return this.callApiPost(`/permission`, postData);
    }
    async update(postData) {
        return this.callApiPut(`/permission`, postData);
    }
    async delete(id) {
        return this.callApiDelete(`/permission?_ids=${id}`);
    }
}
export default PermissionService
/* route.js
            {
              path: "permission",
              name: "app-permission",
              component: () => import("@/views/App/pages/app/Permission"),
              meta: {
                breadcrumb: [
                  { text: "nav.dashboard", href: "/", disabled: false, i18n: true },
                  { text: "model.permission.permission", href: "", disabled: true, i18n: true }
                ],
                pageTitle: { text: "nav.permission.permission", i18n: true }
              }
            },
*/

/* UseMenuApi.js
        {
          title: "model.permission.permission",
          i18n: true,
          icon: "mdi-file-outline",
          to: "/app/permission"
         }
*/
