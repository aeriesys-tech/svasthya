<template>
    <!-- <div>
        <Menu v-if="$store.getters.user"></Menu>
        <Header v-if="$store.getters.user"></Header>
        <div class="main main-app p-3 p-lg-4">
            <router-view/>
        </div>
    </div> -->

    <div v-if="$store.getters.user">
        <Menu></Menu>
        <Header></Header>
        <div class="main main-app p-3 p-lg-4">
            <router-view/>
        </div>
    </div>
    <div v-else>
        <router-view/>
    </div>
</template>
<script>
import Menu from "./components/Menu.vue";
import Header from "./components/Header.vue";
// import Footer from "./components/Footer.vue";

export default {
    name: "App",
    components: { Menu, Header },

    created() {


        // let main = document.createElement("script");
        // main.setAttribute("src", "assets/js/script.js");
		// 	document.head.appendChild(main);


        //Read the status information in sessionStorage when the page is loaded
        if (sessionStorage.getItem("user")) {
            this.$store.dispatch('setUser', JSON.parse(sessionStorage.getItem("user")))
        }
        if (sessionStorage.getItem("token")) {
            this.$store.dispatch('setToken', sessionStorage.getItem("token"))
        }
        if (sessionStorage.getItem("permissions")) {
			this.$store.dispatch('setPermissions', JSON.parse(sessionStorage.getItem("permissions")))
			sessionStorage.removeItem('permissions')
        }
         if (sessionStorage.getItem("current_page")) {
            this.$store.dispatch('setCurrentPage', sessionStorage.getItem("current_page"))
        }
        if (sessionStorage.getItem("assets")) {
            this.$store.dispatch('setAssets', JSON.parse(sessionStorage.getItem("assets")))
        }
        if (sessionStorage.getItem("asset_id")) {
            this.$store.dispatch('setAssetId', sessionStorage.getItem("asset_id"))
        }
        // if (sessionStorage.getItem("dashboard_phase_id")) {
        //     this.$store.dispatch('setDashboardPhaseId', sessionStorage.getItem("dashboard_phase_id"))
        // }
        // if (sessionStorage.getItem("dashboard_area_id")) {
        //     this.$store.dispatch('setDashboardAreaId', sessionStorage.getItem("dashboard_area_id"))
        // }
        // if (sessionStorage.getItem("dashboard_plant_id")) {
        //     this.$store.dispatch('setDashboardPlantId', sessionStorage.getItem("dashboard_plant_id"))
        // }
        // if (sessionStorage.getItem("dashboard_asset_id")) {
        //     this.$store.dispatch('setDashboardAssetId', sessionStorage.getItem("dashboard_asset_id"))
        // }
        if (sessionStorage.getItem("dashboard_asset_type_id")) {
            this.$store.dispatch('setDashboardAssetTypeId', sessionStorage.getItem("sahboard_asset_type_id"))
        }
        //Save the information in vuex to sessionStorage when the page is refreshed
        window.addEventListener("beforeunload", () => {
            sessionStorage.setItem("user", JSON.stringify(this.$store?.getters?.user));
            sessionStorage.setItem("token", this.$store?.getters?.token);
            sessionStorage.setItem("current_page", this.$store?.getters?.current_page);
            sessionStorage.setItem("permissions", JSON.stringify(this.$store?.getters?.permissions))
            sessionStorage.setItem("assets", JSON.stringify(this.$store?.getters?.assets));
            sessionStorage.setItem("asset_id", this.$store?.getters?.asset_id);
            sessionStorage.setItem("dashboard_asset_id", this.$store?.getters?.dashboard_asset_id);
            sessionStorage.setItem("dashboard_plant_id", this.$store?.getters?.dashboard_plant_id);
            sessionStorage.setItem("dashboard_area_id", this.$store?.getters?.dashboard_area_id);
            sessionStorage.setItem("dashboard_phase_id", this.$store?.getters?.dashboard_phase_id);
            sessionStorage.setItem("dashboard_asset_type_id", this.$store?.getters?.dashboard_asset_type_id);
        })
    }
};
</script>

<style>
.icon_hgt {
    font-size: 16px;
    vertical-align: middle;
}
.width-75{
    width: 75px;
}

</style>
