import Service from './Service';

class UserService extends Service {
    constructor() {
        super();
    }
    async get(pageParam) {
        return this.callApiGet(`/user${pageParam}`);
    }
    async create(postData) {
        return this.callApiPost(`/user`, postData);
    }
    async update(postData) {
        return this.callApiPut(`/user`, postData);
    }
    async delete(id) {
        return this.callApiDelete(`/user?_ids=${id}`);
    }
}
export default UserService
/* route.js
            {
              path: "user",
              name: "app-user",
              component: () => import("@/views/App/pages/app/User"),
              meta: {
                breadcrumb: [
                  { text: "nav.dashboard", href: "/", disabled: false },
                  { text: "model.user.user", href: "", disabled: true }
                ],
                pageTitle: { text: "nav.user.user"}
              }
            },
*/

/* UseMenuApi.js
        {
          title: "model.user.user",
          icon: "mdi-file-outline",
          to: "/app/user"
         }
*/
