<template>
  <v-container
    id="page-edrColleage"
    fluid
  >

    <base-wee-sketlon-loader
      :loading="loading"
      type="table-heading, table-thead, table-tbody, table-tfoot"
      :no="1"
    />

    <!-- Table  -->
    <wee-simple-table
      v-if="!loading"
      :headers="fillableHeaders"
      :title="$t('model.edr_colleage.edr_colleage')"
      :tr-list="filteredList"
      :pages="pages"
      :sort="sort"
      @update-search="searchTxt = $event"
      @on-item-click="onItemClick"
      @on-item-delete="onBeforeDeleteItem"
      @on-open-new-form="onOpenNewForm"
      @on-advance-search="advanceSearch"
      @on-reload-page="onReload"
    >
      <!-- <template v-slot:theader></template> 
      <template v-slot:tbody></template> 
      <template v-slot:tpaging></template>  -->
    </wee-simple-table>

    <edr-colleage-form v-model="entity" :edit-mode="editMode" :open="openNewForm" :processing="isProcessing" @close="openNewForm = false" @save="onSave"/>
    <wee-confirm ref="weeConfirmRef"></wee-confirm>
    <wee-toast ref="weeToastRef"></wee-toast>
  </v-container>
</template>

<script>
import { vLog } from "@/plugins/util";
import { getDateWithDefaultFormat } from "@/plugins/dateUtil";
//service
import EdrColleageService from "@/api/EdrColleageService";
import useCrudApi from "@/composition/UseCrudApi";
import { toRefs, onBeforeUnmount} from "@vue/composition-api";
export default {
  name: "page-edrColleage",
  components: {
    WeeConfirm: () => import("@/components/WeeConfirm"),
    WeeToast: () => import("@/components/WeeToast"),
    WeeSimpleTable: () => import("@/components/WeeSimpleTable"),
    EdrColleageForm: () => import("./EdrColleageForm"),
  },
  setup(props, { refs, root }) {
    const edrColleageService = new EdrColleageService();
//column, label, searchable, sortable, fillable, image, avatar status, date, datetime 
    const tableHeaders = [
      {
        column: "ipaddress",
        label: "model.edr_colleage.ipaddress",
        searchable: true,
        sortable: true,
        fillable: true,
        //linkable: {external: true},
      },
      {
        column: "domain",
        label: "model.edr_colleage.domain",
        searchable: true,
        sortable: true,
        fillable: true,
        //linkable: {external: true},
      },
      {
        column: "school_id",
        label: "model.edr_colleage.school_id",
        searchable: true,
        sortable: true,
        fillable: true,
        //linkable: {external: true},
      },
      {
        column: "college_code",
        label: "model.edr_colleage.college_code",
        searchable: true,
        sortable: true,
        fillable: true,
        //linkable: {external: true},
      },
      {
        column: "name_th",
        label: "model.edr_colleage.name_th",
        searchable: true,
        sortable: true,
        fillable: true,
        //linkable: {external: true},
      },
      {
        column: "name_en",
        label: "model.edr_colleage.name_en",
        searchable: true,
        sortable: true,
        fillable: true,
        //linkable: {external: true},
      },
      {
        column: "java_ipaddress",
        label: "model.edr_colleage.java_ipaddress",
        searchable: true,
        sortable: true,
        fillable: true,
        //linkable: {external: true},
      },
      {
        column: "php_ipaddress",
        label: "model.edr_colleage.php_ipaddress",
        searchable: true,
        sortable: true,
        fillable: true,
        //linkable: {external: true},
      },
      {
        column: "sockets_path",
        label: "model.edr_colleage.sockets_path",
        searchable: true,
        sortable: true,
        fillable: true,
        //linkable: {external: true},
      },
      {
        column: "use_std_api",
        label: "model.edr_colleage.use_std_api",
        searchable: true,
        sortable: true,
        fillable: true,
    status: true,
        //linkable: {external: true},
      },
      {
        column: "ssl_expire",
        label: "model.edr_colleage.ssl_expire",
        searchable: true,
        sortable: true,
        fillable: true,
    date: true,
        //linkable: {external: true},
      },
      {
        column: "storage",
        label: "model.edr_colleage.storage",
        searchable: true,
        sortable: true,
        fillable: true,
        //linkable: {external: true},
      },
      {
        column: "ram",
        label: "model.edr_colleage.ram",
        searchable: true,
        sortable: true,
        fillable: true,
        //linkable: {external: true},
      },
      {
        column: "cpu",
        label: "model.edr_colleage.cpu",
        searchable: true,
        sortable: true,
        fillable: true,
        //linkable: {external: true},
      },
      {
        column: "status",
        label: "model.edr_colleage.status",
        searchable: true,
        sortable: true,
        fillable: true,
    status: true,
        //linkable: {external: true},
      },
      {
        label: "base.tool",
        fillable: true,
        baseTool: true
      }
    ];

    //entity
    const initialItem = {
      id: 0,
      ipaddress: '',
      domain: '',
      school_id: '',
      college_code: '',
      name_th: '',
      name_en: '',
      java_ipaddress: '',
      php_ipaddress: '',
      sockets_path: '',
      use_std_api: false,
      ssl_expire: getDateWithDefaultFormat(),
      storage: 0,
      ram: 0,
      cpu: 0,
      status: false,
      created_user: 0,
      created_at: '',
      updated_user: 0,
      updated_at: '',
    };

    const {
      state,
      sort,
      pages,
      filteredList,
      entity,
      isProcessing,
      searchTxt,
      fillableHeaders,
      editMode,
      //method
      fetchData,
      openNewForm,
      onItemClick,
      onOpenNewForm,
      onBeforeDeleteItem,
      onSave,
      advanceSearch,
      onReload
    } = useCrudApi(refs, root, edrColleageService, initialItem, tableHeaders);

    //fell free to change sort colunm and mode
    //sort.column = "id";
    //sort.mode = "ASC";

    onBeforeUnmount(()=>{
      vLog('onBeforeUnmount')
    });

    return {
      ...toRefs(state),
      sort,
      pages,
      filteredList,
      entity,
      isProcessing,
      searchTxt,
      fillableHeaders,
      editMode,
      //method
      fetchData,
      openNewForm,
      onItemClick,
      onOpenNewForm,
      onBeforeDeleteItem,
      onSave,
      advanceSearch,
      onReload
    };
  }
};
</script>
