import Service from './Service';

class EdrColleageService extends Service {
    constructor() {
        super();
    }
    async get(pageParam) {
        return this.callApiGet(`/edrColleage${pageParam}`);
    }
    async create(postData) {
        return this.callApiPost(`/edrColleage`, postData);
    }
    async update(postData) {
        return this.callApiPut(`/edrColleage`, postData);
    }
    async delete(id) {
        return this.callApiDelete(`/edrColleage?_ids=${id}`);
    }
}
export default EdrColleageService
/* route.js
            {
              path: "edr-colleage",
              name: "app-edr-colleage",
              component: () => import("@/views/App/pages/app/EdrColleage"),
              meta: {
                breadcrumb: [
                  { text: "nav.dashboard", href: "/", disabled: false },
                  { text: "model.edr_colleage.edr_colleage", href: "", disabled: true }
                ],
                pageTitle: { text: "model.edr_colleage.edr_colleage", icon: "mdi-api"}
              }
            },
*/

/* UseMenuApi.js
        {
          title: "model.edr_colleage.edr_colleage",
          icon: "mdi-file-outline",
          to: "/app/edr-colleage"
         }
*/
