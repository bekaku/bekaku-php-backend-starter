import Service from './Service';

class EdrStdMapperService extends Service {
    constructor() {
        super();
    }
    async get(pageParam) {
        return this.callApiGet(`/edrStdMapper${pageParam}`);
    }
    async create(postData) {
        return this.callApiPost(`/edrStdMapper`, postData);
    }
    async update(postData) {
        return this.callApiPut(`/edrStdMapper`, postData);
    }
    async delete(id) {
        return this.callApiDelete(`/edrStdMapper?_ids=${id}`);
    }
}
export default EdrStdMapperService
/* route.js
            {
              path: "edr-std-mapper",
              name: "app-edr-std-mapper",
              component: () => import("@/views/App/pages/app/EdrStdMapper"),
              meta: {
                breadcrumb: [
                  { text: "nav.dashboard", href: "/", disabled: false },
                  { text: "model.edr_std_mapper.edr_std_mapper", href: "", disabled: true }
                ],
                pageTitle: { text: "nav.edr_std_mapper.edr_std_mapper", icon: "mdi-api"}
              }
            },
*/

/* UseMenuApi.js
        {
          title: "model.edr_std_mapper.edr_std_mapper",
          icon: "mdi-file-outline",
          to: "/app/edr-std-mapper"
         }
*/
