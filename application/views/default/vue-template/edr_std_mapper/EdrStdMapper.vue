<template>
  <v-container
    id="page-edrStdMapper"
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
      :title="$t('model.edr_std_mapper.edr_std_mapper')"
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

    <edr-std-mapper-form v-model="entity" :open="openNewForm" :processing="isProcessing" @close="openNewForm = false" @save="onSave"/>
    <wee-confirm ref="weeConfirmRef"></wee-confirm>
    <wee-toast ref="weeToastRef"></wee-toast>
  </v-container>
</template>

<script>
import { vLog } from "@/plugins/util";
//service
import EdrStdMapperService from "@/api/EdrStdMapperService";
import useCrudApi from "@/composition/UseCrudApi";
import { toRefs, onBeforeUnmount} from "@vue/composition-api";
export default {
  name: "page-edrStdMapper",
  components: {
    WeeConfirm: () => import("@/components/WeeConfirm"),
    WeeToast: () => import("@/components/WeeToast"),
    WeeSimpleTable: () => import("@/components/WeeSimpleTable"),
    EdrStdMapperForm: () => import("./EdrStdMapperForm"),
  },
  setup(props, { refs, root }) {
    const edrStdMapperService = new EdrStdMapperService();
//column, label, searchable, sortable, fillable, image, status, date, avatar 
    const tableHeaders = [
      {
        column: "function_name",
        label: "model.edr_std_mapper.function_name",
        searchable: true,
        sortable: true,
        fillable: true,
        //linkable: {external: true},
      },
      {
        column: "std",
        label: "model.edr_std_mapper.std",
        searchable: true,
        sortable: true,
        fillable: true,
        //linkable: {external: true},
      },
      {
        column: "edr",
        label: "model.edr_std_mapper.edr",
        searchable: true,
        sortable: true,
        fillable: true,
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
      function_name: '',
      std: '',
      edr: '',
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
      //method
      fetchData,
      openNewForm,
      onItemClick,
      onOpenNewForm,
      onBeforeDeleteItem,
      onSave,
      advanceSearch,
      onReload
    } = useCrudApi(refs, root, edrStdMapperService, initialItem, tableHeaders);

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
