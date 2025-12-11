<template>
  <div>
    <div class="d-sm-flex align-items-center justify-content-between mb-2">
      <div>
        <ol class="breadcrumb fs-sm mb-1">
          <li class="breadcrumb-item" aria-current="page">
            <router-link to="/dashboard">Dashboard</router-link>
          </li>
          <li class="breadcrumb-item">
            <a href="javascript:void(0)">Reviews</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">PPMS Data</li>
        </ol>
        <h4 class="main-title mb-0">PPMS Data</h4>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-12">
        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button
              class="nav-link"
              :class="{'active': currentTab === 'STLPPMSData', 'tab-blue': currentTab === 'STLPPMSData'}"
              @click="currentTab = 'STLPPMSData'"
              type="button"
              role="tab"
            >
              Steel Ladle (STL)
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button
              class="nav-link"
              :class="{'active': currentTab === 'TLCPPMSData', 'tab-blue': currentTab === 'TLCPPMSData'}"
              @click="currentTab = 'TLCPPMSData'"
              type="button"
              role="tab"
            >
              Torpedo Ladle Car (TLC)
            </button>
          </li>
        </ul>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <keep-alive>
          <component
            :is="currentTabComponent"
            @download-excel="downloadExcel"
          />
        </keep-alive>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import STLPPMSData from '@/components/PPMSData/STLPPMSData.vue'
import TLCPPMSData from '@/components/PPMSData/TLCPPMSData.vue'

const currentTab = ref('STLPPMSData')

const currentTabComponent = computed(() => {
  if (currentTab.value === 'STLPPMSData') {
    return STLPPMSData
  }
  if (currentTab.value === 'TLCPPMSData') {
    return TLCPPMSData
  }
  return STLPPMSData
})

function downloadExcel() {
  // This function can be listened for by children if needed
}
</script>

<style scoped>
.tab-blue {
  color: #fff !important;
  background-color: #007bff !important;
  border-color: #007bff #007bff #fff !important;
}
.nav-tabs .nav-link.active.tab-blue {
  color: #fff !important;
  background-color: #007bff !important;
  border-color: #007bff #007bff #fff !important;
}
</style>

