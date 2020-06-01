import Service from './Service';

class RoleService extends Service {
    constructor() {
        super();
    }
    async get(pageParam) {
        return this.callApiGet(`/role${pageParam}`);
    }
    async create(postData) {
        return this.callApiPost(`/role`, postData);
    }
    async update(postData) {
        return this.callApiPut(`/role`, postData);
    }
    async delete(id) {
        return this.callApiDelete(`/role?_ids=${id}`);
    }
}
export default RoleService
/* route.js
            {
              path: "role",
              name: "app-role",
              component: () => import("@/views/App/pages/app/Role"),
              meta: {
                breadcrumb: [
                  { text: "nav.dashboard", href: "/", disabled: false },
                  { text: "model.role.role", href: "", disabled: true }
                ],
                pageTitle: { text: "nav.role.role"}
              }
            },
*/

/* UseMenuApi.js
        {
          title: "model.role.role",
          icon: "mdi-file-outline",
          to: "/app/role"
         }
*/
