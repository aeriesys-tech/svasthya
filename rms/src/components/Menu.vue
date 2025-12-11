<template>
	<div class="sidebar sidebar-light">
		<div class="sidebar-header">
			<router-link to="/dashboard" class="sidebar-logo"><img src="../../src/assets/jsw.png" width="150" /><span
					class="version-number">v1.3.3</span></router-link>
		</div>
		<div id="sidebarMenu" class="sidebar-body">
			<div class="nav-group show">
				<ul class="nav nav-sidebar">
					<li class="nav-item">
						<router-link to="/dashboard" :style="{ color: dashboardActive }" @click="showTab('Dashboard')"
							class="nav-link"><i class="ri-command-line"></i> <span>Dashboard</span></router-link>
					</li>
				</ul>
			</div>
			<div class="nav-group" v-if="permission('UserManagement')" :class="{ show: showUser }"
				@click="showTab('UserManagement')">
				<a href="javascript:void(0)" :style="{ color: userManagementColor }" class="nav-label"><i
						class="ri-user-settings-line icn"></i> User Management</a>
				<ul class="nav nav-sidebar">
					<li class="nav-item" v-can="'roles.view'">
						<router-link to="/role" v-bind:class="{ active: $route.path === '/role' }"
							class="nav-link pl-15"><i class="ri-database-2-fill"></i> <span>Roles</span></router-link>
					</li>
					<li class="nav-item" v-can="'users.view'">
						<router-link to="/users"
							v-bind:class="{ active: $route.path === '/users' || $route.name === 'Users.Create' || $route.path === 'Users.Edit' }"
							class="nav-link pl-15">
							<i class="ri-user-line"></i> <span>Users</span>
						</router-link>
					</li>
					<li class="nav-item" v-can="'permissions.view'">
						<router-link to="/permissions" v-bind:class="{ active: $route.path === '/permissions' }"
							class="nav-link pl-15"><i class="ri-sensor-fill"></i> <span>Permissions</span></router-link>
					</li>
				</ul>
			</div>
			<div class="nav-group" :class="{ show: showLinage }" @click="showTab('LineageParameters')"
				v-if="permission('LineageParameters')">
				<a href="javascript:void(0)" :style="{ color: color }" class="nav-label"><i
						class="ri-equalizer-line icn"></i> Lineage parameters</a>
				<ul class="nav nav-sidebar">
					<li class="nav-item" v-can="'phases.view'">
						<router-link to="/phases" v-bind:class="{ active: $route.path === '/phases' }"
							class="nav-link pl-15"><i class="ri-flag-line"></i> <span>Phases</span></router-link>
					</li>
					<li class="nav-item" v-can="'areas.view'">
						<router-link to="/areas" v-bind:class="{ active: $route.path === '/areas' }"
							class="nav-link pl-15"><i class="ri-command-line"></i> <span>Areas</span></router-link>
					</li>
					<li class="nav-item" v-can="'shops.view'">
						<router-link to="/plants" v-bind:class="{ active: $route.path === '/plants' }"
							class="nav-link pl-15"><i class="ri-building-fill"></i> <span>Shops</span></router-link>
					</li>
					<li class="nav-item" v-can="'sections.view'">
						<router-link to="/section" v-bind:class="{ active: $route.path === '/section' }"
							class="nav-link pl-15"><i class="ri-stack-line"></i> <span>Section</span></router-link>
					</li>
					<!-- <li class="nav-item">
                        <router-link to="/frequency" v-bind:class="{ active: $route.path === '/frequency' }" class="nav-link"><i class="ri-rfid-line"></i> <span>Frequency</span></router-link>
                    </li>     -->
					<li class="nav-item" v-can="'departments.view'">
						<router-link to="/department" v-bind:class="{ active: $route.path === '/department' }"
							class="nav-link pl-15"><i class="ri-government-line"></i>
							<span>Department</span></router-link>
					</li>
					<li class="nav-item" v-can="'functionals.view'">
						<router-link to="/functional" v-bind:class="{ active: $route.path === '/functional' }"
							class="nav-link pl-15"><i class="ri-rfid-line"></i> <span>Functional</span></router-link>
					</li>
				</ul>
			</div>
			<div class="nav-group show">
				<ul class="nav nav-sidebar">
					<li class="nav-item" v-can="'list_parameters.view'">
						<router-link to="/list_parameters" :style="{ color: listActive }" @click="showTab('List')"
							class="nav-link"><i class="ri-command-line"></i> <span>List Parameters</span></router-link>
					</li>
				</ul>
			</div>
			<div class="nav-group" :class="{ show: showType }" @click="showTab('TypeParameters')"
				v-if="permission('TypeParameters')">
				<a href="javascript:void(0)" :style="{ color: typeParameterColor }" class="nav-label"><i
						class="ri-corner-up-right-double-line icn"></i> Type Parameters</a>
				<ul class="nav nav-sidebar">
					<!-- <li class="nav-item" v-can="'equipment_types.view'">
                        <router-link to="/equipment_types" v-bind:class="{ active: $route.path === '/equipment_types' }" class="nav-link"><i class="ri-equalizer-line"></i> <span>Equipment Type</span></router-link>
                    </li> -->
					<li class="nav-item" v-can="'asset_types.view'">
						<router-link to="/asset_types" v-bind:class="{ active: $route.path === '/asset_types' }"
							class="nav-link pl-15"><i class="ri-building-3-line"></i> <span>Asset
								Types</span></router-link>
					</li>
					<li class="nav-item" v-can="'service_types.view'">
						<router-link to="/service_types" v-bind:class="{ active: $route.path === '/service_types' }"
							class="nav-link pl-15"><i class="ri-layout-fill"></i> <span>Service
								Types</span></router-link>
					</li>
					<li class="nav-item" v-can="'spare_types.view'">
						<router-link to="/spare_types" v-bind:class="{ active: $route.path === '/spare_types' }"
							class="nav-link pl-15"><i class="ri-shapes-line"></i> <span>Spare Types</span></router-link>
					</li>
					<li class="nav-item" v-can="'activity_types.view'">
						<router-link to="/activity_types" v-bind:class="{ active: $route.path === '/activity_types' }"
							class="nav-link pl-15"><i class="ri-color-filter-line"></i> <span>Activity
								Types</span></router-link>
					</li>
					<li class="nav-item" v-can="'data_source_types.view'">
						<router-link to="/data_source_type"
							v-bind:class="{ active: $route.path === '/data_source_type' }" class="nav-link pl-15"><i
								class="ri-line-chart-line"></i> <span>Data Source Types</span></router-link>
					</li>
					<li class="nav-item" v-can="'break_down_types.view'">
						<router-link to="/break_down_type" v-bind:class="{ active: $route.path === '/break_down_type' }"
							class="nav-link pl-15"><i class="ri-bubble-chart-line"></i> <span>Break Down
								Types</span></router-link>
					</li>
					<li class="nav-item" v-can="'accessory_types.view'">
						<router-link to="/accessory_type" v-bind:class="{ active: $route.path === '/accessory_type' }"
							class="nav-link pl-15"><i class="ri-briefcase-line"></i> <span>Accessory
								Types</span></router-link>
					</li>
					<li class="nav-item" v-can="'variable_types.view'">
						<router-link to="/variable_type" v-bind:class="{ active: $route.path === '/variable_type' }"
							class="nav-link pl-15"><i class="ri-infinity-line"></i> <span>Variable
								Types</span></router-link>
					</li>
				</ul>
			</div>

			<!-- <div class="nav-group show">
                <ul class="nav nav-sidebar">
                    <li class="nav-item" v-can="'assetParameters.view'">
                        <router-link to="/asset_parameters" :style="{color:AssetParametersColor}" @click="showTab('AssetParameters')" class="nav-link"><i class="ri-stack-fill"></i> <span>Asset Parameters</span></router-link>
                    </li>
                </ul>
            </div> -->
			<div class="nav-group" :class="{ show: showAttributes }" @click="showTab('Attributes')"
				v-if="permission('Attributes')">
				<a href="javascript:void(0)" class="nav-label" :style="{ color: AttributesColor }"><i
						class="ri-stack-fill icn"></i> Attributes</a>
				<ul class="nav nav-sidebar">
					<li class="nav-item" v-can="'asset_attributes.view'">
						<router-link to="/asset_attributes"
							v-bind:class="{ active: $route.path === '/asset_attributes' || $route.name === 'AssetAttributes.Create' || $route.path === 'AssetAttributes.Edit' }"
							class="nav-link pl-15"><i class="ri-building-3-line"></i> <span>Asset
								Attribute</span></router-link>
					</li>
					<li class="nav-item" v-can="'spare_attributes.view'">
						<router-link to="/spare_attributes"
							v-bind:class="{ active: $route.path === '/spare_attributes' || $route.name === 'SpareAttributes.Create' || $route.path === 'SpareAttributes.Edit' }"
							class="nav-link pl-15"><i class="ri-shapes-line"></i> <span>Spare
								Attribute</span></router-link>
					</li>
					<li class="nav-item" v-can="'data_source_attributes.view'">
						<router-link to="/data_source_attributes"
							v-bind:class="{ active: $route.path === '/data_source_attributes' || $route.name === 'DataSourceAttributes.Create' || $route.path === 'DataSourceAttributes.Edit' }"
							class="nav-link pl-15"><i class="ri-line-chart-line"></i> <span>Data Source
								Attribute</span></router-link>
					</li>
					<li class="nav-item" v-can="'variable_attributes.view'">
						<router-link to="/variable_attributes"
							v-bind:class="{ active: $route.path === '/variable_attributes' || $route.name === 'VariableAttributes.Create' || $route.path === 'VariableAttributes.Edit' }"
							class="nav-link pl-15"><i class="ri-infinity-line"></i> <span>Variable
								Attribute</span></router-link>
					</li>
					<li class="nav-item" v-can="'service_attributes.view'">
						<router-link to="/service_attributes"
							v-bind:class="{ active: $route.path === '/service_attributes' || $route.name === 'ServiceAttributes.Create' || $route.path === 'ServiceAttributes.Edit' }"
							class="nav-link pl-15"><i class="ri-layout-fill"></i> <span>Service
								Attribute</span></router-link>
					</li>
					<li class="nav-item" v-can="'break_down_attributes.view'">
						<router-link to="/break_down_attributes"
							v-bind:class="{ active: $route.path === '/break_down_attributes' || $route.name === 'BreakDownAttributes.Create' || $route.path === 'BreakDownAttributes.Edit' }"
							class="nav-link pl-15"><i class="ri-bubble-chart-line"></i> <span>Breakdown
								Attribute</span></router-link>
					</li>
					<li class="nav-item" v-can="'activity_attributes.view'">
						<router-link to="/activity_attributes"
							v-bind:class="{ active: $route.path === '/activity_attributes' || $route.name === 'ActivityAttributes.Create' || $route.path === 'ActivityAttributes.Edit' }"
							class="nav-link pl-15"><i class="ri-color-filter-line"></i> <span>Activity
								Attribute</span></router-link>
					</li>
				</ul>
			</div>

			<div class="nav-group" v-if="permission('Masters')" :class="{ show: showList }"
				@click="showTab('ListParameters')">
				<a href="javascript:void(0)" :style="{ color: listParameterColor }" class="nav-label"><i
						class="ri-list-settings-line icn"></i> Masters</a>
				<ul class="nav nav-sidebar">
					<li class="nav-item" v-can="'checks.view'">
						<router-link to="/checks"
							v-bind:class="{ active: $route.path === '/checks' || $route.name === 'Checks.Create' || $route.path === 'Checks.Edit' }"
							class="nav-link pl-15"><i class="ri-command-line"></i> <span>Checks</span></router-link>
					</li>
					<li class="nav-item" v-can="'spares.view'">
						<router-link to="/spares"
							v-bind:class="{ active: $route.path === '/spares' || $route.name === 'Spares.Create' || $route.path === 'Spares.Edit' }"
							class="nav-link pl-15"><i class="ri-align-left"></i> <span>Spares</span></router-link>
					</li>
					<li class="nav-item" v-can="'services.view'">
						<router-link to="/services"
							v-bind:class="{ active: $route.path === '/services' || $route.name === 'Services.Create' || $route.path === 'Services.Edit' }"
							class="nav-link pl-15"><i class="ri-drag-move-2-line"></i>
							<span>Services</span></router-link>
					</li>
					<!-- <li class="nav-item" v-can="'services.view'">
                        <router-link to="/services" v-bind:class="{ active: $route.path === '/services' }" class="nav-link"><i class="ri-drag-move-2-line"></i> <span>Services</span></router-link>
                    </li> -->
					<li class="nav-item" v-can="'variables.view'">
						<router-link to="/variables"
							v-bind:class="{ active: $route.path === '/variables' || $route.name === 'Variables.Create' || $route.path === 'Variables.Edit' }"
							class="nav-link pl-15"><i class="ri-archive-drawer-line"></i>
							<span>Variables</span></router-link>
					</li>
					<li class="nav-item" v-can="'data_sources.view'">
						<router-link to="/data_sources"
							v-bind:class="{ active: $route.path === '/data_sources' || $route.name === 'DataSources.Create' || $route.path === 'DataSources.Edit' }"
							class="nav-link pl-15"><i class="ri-line-chart-line"></i> <span>Data
								Source</span></router-link>
					</li>
					<!-- <li class="nav-item">
                        <router-link to="/break_down_lists" v-bind:class="{ active: $route.path === '/break_down_lists' || $route.name === 'BreakDownLists.Create' || $route.path === 'BreakDownLists.Edit' }" class="nav-link"><i class="ri-bubble-chart-line"></i> <span>BreakDown List</span></router-link>
                    </li> -->
				</ul>
			</div>
			<!-- <div class="nav-group" v-if="permission('Masters')" :class="{show:showMaster}" @click="showTab('Masters')">
                <a href="javascript:void(0)" :style="{color:masterColor}" class="nav-label"><i class="ri-settings-5-line icn"></i> Masters</a>
                <ul class="nav nav-sidebar">
                    <li class="nav-item" v-can="'equipment.view'">
                        <router-link to="/equipment" v-bind:class="{ active: $route.path === '/equipment' }" class="nav-link"><img src="assets/images/machine.png" class="me-2" style="width: 22px;" alt=""><span>Equipment</span></router-link>
                    </li>
                    <li class="nav-item" v-can="'assets.view'">
                        <router-link to="/assets" v-bind:class="{ active: $route.path === '/assets' || $route.path === '/asset/create' || $route.name === 'Assets.Edit' || $route.name === 'Assets.View' }" class="nav-link"><i class="ri-briefcase-fill"></i><span>Asset</span></router-link>
                    </li>
                </ul>
            </div> -->
			<div class="nav-group show">
				<ul class="nav nav-sidebar">
					<li class="nav-item" v-can="'asset_templates.view'">
						<router-link to="/asset_templates" :style="{ color: assetTemplateActive }"
							@click="showTab('AssetTemplate')"
							v-bind:class="{ active: $route.path === '/asset_templates' || $route.path === '/asset_template/create' || $route.name === 'AssetTemplates.Edit' || $route.name === 'AssetTemplates.View' }"
							class="nav-link">
							<i class="ri-archive-drawer-line"></i><span>Asset Templates</span>
						</router-link>
					</li>
				</ul>
			</div>
			<div class="nav-group show">
				<ul class="nav nav-sidebar">
					<li class="nav-item" v-can="'assets.view'">
						<router-link to="/assets" :style="{ color: assetActive }" @click="showTab('Asset')"
							v-bind:class="{ active: $route.path === '/assets' || $route.path === '/asset/create' || $route.name === 'Assets.Edit' || $route.name === 'Assets.View' }"
							class="nav-link">
							<i class="ri-briefcase-fill"></i><span>Assets</span>
						</router-link>
					</li>
				</ul>
			</div>

			<div class="nav-group" v-if="permission('Registers')" :class="{ show: showRegister }"
				@click="showTab('Registers')">
				<a href="javascript:void(0)" :style="{ color: registerColor }" class="nav-label"><i
						class="ri-links-fill icn"></i> Registers</a>
				<ul class="nav nav-sidebar">
					<li class="nav-item" v-can="'health_check_registers.view'">
						<router-link
							v-bind:class="{ active: $route.path === '/health_checks' || $route.name === 'HealthChecks.Create' || $route.name === 'HealthChecks.Edit' }"
							to="/health_checks" class="nav-link pl-15">
							<i class="ri-health-book-line"></i><span>Scanner Register</span>
						</router-link>
					</li>
					<li class="nav-item" v-can="'activity_registers.view'">
						<router-link
							v-bind:class="{ active: $route.path === '/activities' || $route.name === 'Activity.Create' || $route.name === 'Activity.Edit' }"
							to="/activities" class="nav-link pl-15">
							<i class="ri-list-check"></i><span>Activity Register</span>
						</router-link>
					</li>
					<li class="nav-item" v-can="'service_registers.view'">
						<router-link
							v-bind:class="{ active: $route.path === '/user_services' || $route.name === 'UserServices.Create' || $route.name === 'UserServices.Edit' }"
							to="/user_services" class="nav-link pl-15">
							<i class="ri-flow-chart"></i><span>Spare Register</span>
						</router-link>
					</li>
					<li class="nav-item" v-can="'check_registers.view'">
						<router-link
							v-bind:class="{ active: $route.path === '/user_checks' || $route.name === 'UserChecks.Create' || $route.name === 'UserChecks.Edit' }"
							to="/user_checks" class="nav-link pl-15">
							<i class="ri-survey-line"></i><span>Inspection Register</span>
						</router-link>
					</li>
					<li class="nav-item" v-can="'process_registers.view'">
						<router-link to="/process_registers"
							v-bind:class="{ active: $route.path === '/process_registers' || $route.name === 'CreateProcessRegister.Create' || $route.name === 'CreateProcessRegister.Edit' }"
							class="nav-link pl-15"><i class="ri-briefcase-fill"></i><span>Process
								Register</span></router-link>
					</li>
					<li class="nav-item" v-can="'break_down_registers.view'">
						<router-link to="/break_down_registers"
							v-bind:class="{ active: $route.path === '/break_down_registers' || $route.name === 'BreakDownRegister.Create' || $route.name === 'BreakDownRegister.Edit' }"
							class="nav-link pl-15"><i class="ri-bubble-chart-line"></i><span>Break Down
								Register</span></router-link>
					</li>
					<li class="nav-item" v-can="'update_vendors.bulk'">
						<router-link to="/bulk_vendors" v-bind:class="{ active: $route.path === '/bulk_vendors' }"
							class="nav-link pl-15"><i class="ri-team-line"></i> <span>Update
								Vendors</span></router-link>
					</li>
					<!-- <li class="nav-item">
                        <router-link to="/spare_registers" v-bind:class="{ active: $route.path === '/spare_registers' }" class="nav-link"><i class="ri-briefcase-fill"></i><span>Spare Register</span></router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/qc_registers" v-bind:class="{ active: $route.path === '/qc_registers' }" class="nav-link"><i class="ri-briefcase-fill"></i><span>QC Register</span></router-link>
                    </li> -->
				</ul>
			</div>

			<!-- <div class="nav-group">
                <a href="javascript:void(0)" class="nav-label"><i class="ri-flow-chart icn"></i> Services Register</a>
                <ul class="nav nav-sidebar">
                    <li class="nav-item">
                        <router-link to="/user_services" class="nav-link"><i class="ri-contacts-line"></i><span>User Services</span></router-link>
                    </li>
                </ul>
            </div>  -->

			<div class="nav-group" :class="{ show: showReview }" @click="showTab('Review')" v-if="permission('Review')">
				<a href="javascript:void(0)" class="nav-label" :style="{ color: ReviewColor }"><i
						class="ri-file-search-line icn"></i> Review</a>
				<ul class="nav nav-sidebar">
					<li class="nav-item" v-can="'asset_details.view'">
						<router-link to="/asset_details" v-bind:class="{ active: $route.path === '/asset_details' }"
							class="nav-link pl-15"><i class="ri-building-3-line"></i> <span>Asset
								Details</span></router-link>
					</li>
					<li class="nav-item" v-can="'spare_campaigns.view'">
						<router-link to="/spare_campaigns" v-bind:class="{ active: $route.path === '/spare_campaigns' }"
							class="nav-link pl-15"><i class="ri-align-left   "></i> <span>Scanner
								Results</span></router-link>
					</li>
					<li class="nav-item" v-can="'process_trends.view'">
						<router-link to="/process_trends" v-bind:class="{ active: $route.path === '/process_trends' }"
							class="nav-link pl-15"><i class="ri-alarm-warning-line"></i> <span>Process
								Trends</span></router-link>
					</li>
					<li class="nav-item" v-can="'ppms_data.view'">
						<router-link to="/ppms_datas" v-bind:class="{ active: $route.path === '/ppms_datas' }"
							class="nav-link pl-15"><i class="ri-bubble-chart-fill"></i><span>PPMS
								Data</span></router-link>
					</li>
					<li class="nav-item" v-can="'tlc_lab_report.view'">
						<router-link to="/tlc_lab_report" v-bind:class="{ active: $route.path === '/tlc_lab_report' }"
							class="nav-link pl-15"><i class="ri-flask-fill"></i><span>Torpedo
								Lab Report</span></router-link>
					</li>
					<li class="nav-item" v-can="'ppms_statistics.view'">
						<router-link to="/ppms_statistics" v-bind:class="{ active: $route.path === '/ppms_statistics' }"
							class="nav-link pl-15"><i class="ri-bar-chart-2-line"></i><span>Running Life
								Summary</span></router-link>
					</li>
					<li class="nav-item">
						<router-link to="/compliance" v-bind:class="{ active: $route.path === '/compliance' }"
							class="nav-link pl-15"><i class="ri-article-line"></i> <span>Compliance</span></router-link>
					</li>
					<li class="nav-item">
						<router-link to="/deviations_review"
							v-bind:class="{ active: $route.path === '/deviations_review' }" class="nav-link pl-15"><i
								class="ri-direction-line"></i> <span>Deviations</span></router-link>
					</li>
					<li class="nav-item" v-can="'alerts.view'">
						<router-link to="/check_alerts" v-bind:class="{ active: $route.path === '/check_alerts' }"
							class="nav-link pl-15"><i class="ri-alarm-warning-line"></i>
							<span>Alerts</span></router-link>
					</li>
				</ul>
			</div>

			<!-- <div class="nav-group show">
                <a href="javascript:void(0)" style="color: inherit;" class="nav-label" @click="toggleReports"> <i class="ri-file-chart-line icn"></i>&nbsp; Reports </a>
                <ul class="nav nav-sidebar" v-show="showReports">
                    <li class="nav-item">
                        <a href="javascript:void(0)" class="nav-link"> <i class="ri-file-chart-2-line"></i> <span>Asset Report</span> </a>
                    </li>
                    <li class="nav-item">
                        <a href="javascript:void(0)" class="nav-link"> <i class="ri-pass-pending-line"></i> <span>Job Pending Report</span> </a>
                    </li>
                    <li class="nav-item">
                        <a href="javascript:void(0)" class="nav-link"> <i class="ri-store-3-fill"></i> <span>Activity Report</span> </a>
                    </li>
                </ul>
            </div> -->

			<div class="nav-group" :class="{ show: showReports }" @click="showTab('Reports')"
				v-if="permission('Reports')">
				<a href="javascript:void(0)" :style="{ color: ReportColor }" class="nav-label">
					<i class="ri-file-chart-line icn"></i>&nbsp; Reports
				</a>
				<ul class="nav nav-sidebar">
					<li class="nav-item" v-can="'asset_reports.view'">
						<router-link to="/asset_reports" class="nav-link pl-15"
							v-bind:class="{ active: $route.path === '/asset_reports' }">
							<i class="ri-file-chart-2-line"></i> <span>Asset Report</span>
						</router-link>
					</li>

					<li class="nav-item" v-can="'job_pending_reports.view'">
						<router-link to="/pending_jobs" class="nav-link pl-15"
							v-bind:class="{ active: $route.path === '/pending_jobs' }">
							<i class="ri-pass-pending-line"></i> <span>Job Pending Report</span>
						</router-link>
					</li>
					<li class="nav-item" v-can="'deviation_reports.view'">
						<router-link to="/deviation_reports" class="nav-link pl-15"
							v-bind:class="{ active: $route.path === '/deviation_reports' }">
							<i class="ri-store-3-fill"></i> <span>Deviation Report</span>
						</router-link>
					</li>
					<li class="nav-item" v-can="'break_down_reports.view'">
						<router-link to="/breakdown_reports" class="nav-link pl-15"
							v-bind:class="{ active: $route.path === '/breakdown_reports' }">
							<i class="ri-file-chart-2-line"></i> <span>BreakDown Report</span>
						</router-link>
					</li>
					<li class="nav-item" v-can="'refractory_consumption_reports.view'">
						<router-link to="/refractory_consumption_reports" class="nav-link pl-15"
							v-bind:class="{ active: $route.path === '/refractory_consumption_reports' }">
							<i class="ri-functions"> </i> <span>Refractory Consumption Report</span>
						</router-link>
					</li>
					<li class="nav-item" v-can="'stl_daily_reports.view'">
						<router-link to="/stl_daily_reports" class="nav-link pl-15"
							v-bind:class="{ active: $route.path === '/stl_daily_reports' }">
							<i class="ri-file-chart-2-line"></i> <span>STL Daily Report</span>
						</router-link>
					</li>
					<li class="nav-item" v-can="'tlc_daily_reports.view'">
						<router-link to="/tlc_daily_reports" class="nav-link pl-15"
							v-bind:class="{ active: $route.path === '/tlc_daily_reports' }">
							<i class="ri-file-text-fill"></i> <span>TLC Daily Report</span>
						</router-link>
					</li>
				</ul>
			</div>

			<!-- <div class="nav-group show">
                <a href="javascript:void(0)" style="color: inherit;" class="nav-label" @click="toggleReports">
                <i class="ri-file-chart-line icn"></i>&nbsp; Reports
                </a>
                <ul class="nav nav-sidebar" v-show="showReports">
                <li class="nav-item">
                    <router-link to="/asset_reports" class="nav-link">
                        <i class="ri-file-chart-2-line"></i> <span>Asset Report</span>
                    </router-link>
                </li>

                <li class="nav-item">
                    <router-link to="/pending_jobs" class="nav-link">
                    <i class="ri-pass-pending-line"></i> <span>Job Pending Report</span>
                    </router-link>
                </li>
                <li class="nav-item">
                    <router-link to="/deviation_reports" class="nav-link">
                    <i class="ri-store-3-fill"></i> <span>Deviation Report</span>
                    </router-link>
                </li>
                <li class="nav-item">
                        <router-link to="/breakdown_reports" class="nav-link">
                            <i class="ri-file-chart-2-line"></i> <span>BreakDown Report</span>
                        </router-link>
                    </li>
                </ul>
            </div> -->


			<div class="nav-group" :class="{ show: showPredictions }" @click="showTab('Predictions')">
				<a href="javascript:void(0)" class="nav-label" :style="{ color: PredictionColor }"><i
						class="ri-copper-coin-line icn"></i> Predictions</a>
				<ul class="nav nav-sidebar">
					<li class="nav-item">
						<router-link to="/asset_life" v-bind:class="{ active: $route.path === '/asset_life' }"
							class="nav-link pl-15"><i class="ri-expand-width-fill"></i> <span>Asset
								Life</span></router-link>
					</li>
					<li class="nav-item">
						<router-link to="/upcoming_repairs"
							v-bind:class="{ active: $route.path === '/upcoming_repairs' }" class="nav-link pl-15"><i
								class="ri-skip-up-fill"></i> <span>Upcoming Repiars</span></router-link>
					</li>
				</ul>
			</div>

			<div class="nav-group show">
				<ul class="nav nav-sidebar">
					<li class="nav-item" v-can="'downloaded_reports.view'">
						<router-link to="/downloaded_reports" :style="{ color: DownloadedReportsColor }"
							@click="showTab('DownloadedReports')" class="nav-link"><i class="ri-download-line"></i>
							<span>Downloaded Reports</span></router-link>
					</li>
				</ul>
			</div>

			<div class="nav-group show">
				<ul class="nav nav-sidebar">
					<li class="nav-item" v-can="'uploaded_logs.view'">
						<router-link to="/offline_logs" :style="{ color: OfflineLogsColor }"
							@click="showTab('DownloadedReports')" class="nav-link"><i
								class="ri-file-upload-line"></i><span>Uploded Logs</span></router-link>
					</li>
				</ul>
			</div>

			<!-- <div class="nav-group">
                <a href="javascript:void(0)" :style="{color:reportsColor}" class="nav-label"><i class="ri-file-copy-2-line icn"></i> Reports</a>
                <ul class="nav nav-sidebar">
                    <li class="nav-item">
                        <router-link to="" class="nav-link"><img src="assets/images/generator.png" style="width: 20px;" class="me-2" alt=""><span>Assets Report</span></router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="" class="nav-link"><img src="assets/images/machine.png" class="me-2" style="width: 22px;" alt=""><span>Equipment Report</span></router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="" class="nav-link"><i class="ri-tools-line"></i><span>Spares Report</span></router-link>
                    </li>
                </ul>
            </div> -->
		</div>
		<div class="sidebar-footer">
			<div class="sidebar-footer-top">
				<div class="sidebar-footer-thumb">
					<!-- <img src="" alt="" /> -->
					<div class="avatar online" v-if="$store.getters.user?.avatar"><img
							:src="$store.getters.user?.avatar" alt="" /></div>
					<div class="avatar online" v-else><img src="../../public/assets/images/default_avatar.png" alt="" />
					</div>
				</div>

				<div class="sidebar-footer-body">
					<h6>{{ $store.getters?.user?.name }}</h6>
					<p>{{ $store.getters.user.role?.role }}</p>
				</div>
				<a id="sidebarFooterMenu" href="#" class="dropdown-link"><i class="ri-arrow-down-s-line"></i></a>
			</div>
			<!-- <div class="sidebar-footer-menu">
                <nav class="nav">
                    <router-link class="dropdown-item" to="/profile" ><i class="ri-edit-2-line"></i> Edit Profile</router-link>
                    <a href="javascript:void(0)" @click="logout()"><i class="ri-logout-box-r-line"></i> Log Out</a>
                </nav>
            </div> -->

		</div>
	</div>
</template>
<script>
export default {
	data() {
		return {
			permissions: this.$store.getters.permissions,
			dashboardActive: "blue !important",
			color: "#41505f !important",
			typeParameterColor: "#41505f !important",
			motorParameterColor: "#41505f !important",
			listParameterColor: "#41505f !important",
			masterColor: "#41505f !important",
			registerColor: "#41505f !important",
			userManagementColor: "#41505f !important",
			reportsColor: "#41505f !important",
			AssetParametersColor: "#41505f !important",
			AttributesColor: "#41505f !important",
			DownloadedReportsColor: '#41505f !important',
			OfflineLogsColor: '#41505f !important',
			assetTemplateActive: "blue  !important",
			assetActive: "blue  !important",
			listsActive: "blue  !important",
			showLinage: false,
			showType: false,
			showMotor: false,
			showList: false,
			showAttributes: false,
			showMaster: false,
			showRegister: false,
			showUser: false,
			AssetParametersActive: false,
			showReports: false,
			showReview: false,
			showPredictions: false,
			DownloadedReportsActive: false,
			OfflineLogsActive: false,
		};
	},
	watch: {
		$route(to) {
			this.showTab(to.path, to.name);
		},
	},
	methods: {
		toggleReports() {
			this.showReports = !this.showReports;
		},
		logout() {
			let vm = this;
			let loader = vm.$loading.show();
			vm.$store
				.dispatch("post", { uri: "logout", data: vm.$store.getters.user })
				.then(function () {
					loader.hide();
					vm.$store.dispatch("logout");
					vm.$router.push("/");
				})
				.catch(function (error) {
					loader.hide();
					vm.errors = error.response.data.errors;
					vm.$store.dispatch("error", error.response.data.message);
				});
		},
		showTab(tab, name) {
			this.dashboardActive = "#41505f !important";
			this.color = "#41505f !important";
			this.typeParameterColor = "#41505f !important";
			this.motorParameterColor = "#41505f !important";
			this.listParameterColor = "#41505f !important";
			this.masterColor = "#41505f !important";
			this.registerColor = "#41505f !important";
			this.userManagementColor = "#41505f !important";
			this.reportsColor = "#41505f !important";
			this.AssetParametersColor = "#41505f !important";
			this.assetTemplateActive = "#41505f !important";
			this.assetActive = "#41505f !important";
			this.AttributesColor = "#41505f !important";
			this.ReviewColor = "#41505f !important";
			this.ReportColor = "#41505f !important";
			this.PredictionColor = "#41505f !important";
			this.listActive = "#41505f !important";
			this.DownloadedReportsColor = '#41505f !important';
			this.OfflineLogsColor = '#41505f !important'

			if (tab == "/areas" || tab == "/plants" || tab == "/section" || tab == "/frequency" || tab == "/department" || tab == "/functional" || tab == '/phases') {
				this.color = "blue !important";
				this.showLinage = !this.showLinage;
			} else if (
				tab == "/equipment_types" ||
				tab == "/asset_types" ||
				tab == "/service_types" ||
				tab == "/spare_types" ||
				tab == "/activity_types" ||
				tab == "/data_source_type" ||
				tab == "/break_down_type" ||
				tab == "/accessory_type" ||
				tab == "/variable_type"
			) {
				// this.typeParameterColor = "white !important";
				this.typeParameterColor = "blue !important";
				this.showType = !this.showType;
			} else if (tab == "/voltages" || tab == "/watt_rating" || tab == "/frame" || tab == "/mounting" || tab == "/make" || tab == "/speed") {
				this.motorParameterColor = "white !important";
				this.showMotor = !this.showMotor;
			}
			// else if(tab == '/asset_parameters'){
			//     this.AssetParametersColor='white !important';
			//     this.AssetParametersActive = !this.AssetParametersActive
			// }
			else if (tab == "/checks" || name === "Check.Create" || name === "Checks.Edit" || tab == "/spares" || name == 'Spares.Create' || tab == "/services" || tab == "/variables" || tab == "/data_sources" || tab == "/break_down_lists") {
				// this.listParameterColor = "white !important";
				this.listParameterColor = "blue !important";
				this.showList = !this.showList;
			}
			// else if(tab == '/equipment' || tab == '/assets' || name == 'Assets.Create' || name=='Assets.Edit' || name == 'Assets.View'){
			//     this.masterColor='white !important';
			//     this.showMaster = !this.showMaster;
			// }
			else if (
				tab == "/health_checks" ||
				name == "HealthChecks.Create" ||
				name == "HealthChecks.Edit" ||

				tab == "/activities" ||
				name == "Activity.Create" ||
				name == "Activity.Edit" ||

				tab == "/user_services" ||
				name == "UserServices.Create" ||
				name == "UserServices.Edit" ||

				tab == "/user_checks" ||
				name == "UserChecks.Create" ||
				name == "UserChecks.Edit" ||

				tab == "/process_registers" ||
				name == "/CreateProcessRegister.Create" ||
				name == "/CreateProcessRegister.Edit" ||

				tab == "/break_down_registers" ||
				name == "/BreakDownRegister.Create" ||
				name == "/BreakDownRegister.Edit" ||

				tab == "/bulk_vendors" ||
				tab == "/spare_registers" ||
				tab == "/qc_registers"
			) {
				this.registerColor = "blue  !important";
				this.showRegister = !this.showRegister;
			} else if (tab == "/role" || tab == "/users" || name == "Users.Create" || name == "Users.Edit" || tab == "/permissions") {
				this.userManagementColor = "blue  !important";
				this.showUser = !this.showUser;
			} else if (tab == "/dashboard") {
				this.dashboardActive = "blue !important";
			} else if (tab == "/asset_templates" || name == "AssetTemplates.Create" || name == "AssetTemplates.Edit" || name == "AssetTemplates.View") {
				this.assetTemplateActive = "blue  !important";
			}
			else if (tab == "/assets" || name == "Assets.Create" || name == "Assets.Edit" || name == "Assets.View") {
				this.assetActive = "blue  !important";
			} else if (tab == "/asset_attributes" || tab == "/spare_attributes" || tab == "/data_source_attributes" || tab == "/variable_attributes" || tab == "/service_attributes" || tab == "/break_down_attributes" || tab == "/activity_attributes") {
				this.AttributesColor = "blue  !important";
				this.showAttributes = !this.showAttributes;
			} else if (tab == "/asset_details" || tab == "/spare_campaigns" || tab == "/compliance" || tab == "/deviations_review" || tab == "/alerts" || tab == "/process_trends" || tab == '/ppms_datas' || tab == '/tlc_lab_report' || tab == '/ppms_statistics' || tab == '/check_alerts') {
				this.ReviewColor = "blue  !important";
				this.showReview = !this.showReview;
			} else if (tab == "/asset_life" || tab == "/upcoming_repairs") {
				this.PredictionColor = "blue  !important";
				this.showPredictions = !this.showPredictions;
			}
			else if (tab == "/list_parameters") {
				this.listActive = "blue  !important";
			}
			else if (tab == "/asset_reports" || tab == "/pending_jobs" || tab == "/deviation_reports" || tab == "/breakdown_reports" || tab == '/refractory_consumption_reports' || tab == '/stl_daily_reports' || tab == '/tlc_daily_reports') {
				this.ReportColor = "blue  !important";
				this.showReports = !this.showReports;
			}
			else if (tab == '/downloaded_reports') {
				this.DownloadedReportsColor = 'blue  !important';
				this.DownloadedReportsActive = !this.DownloadedReportsActive
			}
			else if (tab == '/offline_logs') {
				this.OfflineLogsColor = 'blue  !important';
				this.OfflineLogsActive = !this.OfflineLogsActive
			}

			switch (tab) {
				case "Dashboard":
					this.dashboardActive = "blue !important";
					break;
				case "List":
					this.listActive = "blue  !important";
					break;
				case "LineageParameters":
					this.color = "blue  !important";
					this.showLinage = !this.showLinage;
					break;
				case "TypeParameters":
					this.typeParameterColor = "blue  !important";
					this.showType = !this.showType;
					this.showLinage = false;
					break;
				case "MotorParameters":
					this.motorParameterColor = "blue  !important";
					this.showMotor = !this.showMotor;
					break;
				case "ListParameters":
					this.listParameterColor = "blue  !important";
					this.showList = !this.showList;
					break;
				case "Attributes":
					this.AttributesColor = "blue  !important";
					this.showAttributes = !this.showAttributes;
					break;
				case "Masters":
					this.masterColor = "blue  !important";
					this.showMaster = !this.showMaster;
					break;
				case "Registers":
					this.registerColor = "blue  !important";
					this.showRegister = !this.showRegister;
					break;
				case "UserManagement":
					this.userManagementColor = "blue  !important";
					this.showUser = !this.showUser;
					break;
				case "Asset":
					this.assetTemplateActive = "blue  !important";
					break;
				case "Asset":
					this.assetActive = "blue  !important";
					break;
				case "Review":
					this.ReviewColor = "blue  !important";
					this.showReview = !this.showReview;
					break;
				case "Reports":
					this.ReportColor = "blue  !important";
					this.showReports = !this.showReports;
					break;
				case "Predictions":
					this.PredictionColor = "blue  !important";
					this.showPredictions = !this.showPredictions;
					break;
				case "Downloaded Reports":
					this.DownloadedReportsColor = "blue  !important";
					this.DownloadedReportsActive = !this.DownloadedReportsActive;
					break;
				case "Uploded Logs":
					this.OfflineLogsColor = "blue  !important";
					this.OfflineLogsActive = !this.OfflineLogsActive;
				default:
					break;
			}
		},
		permission(ability) {
			let permissions = this.$store.getters.permissions;
			if (permissions && permissions.length != 0) {
				let permission = [];
				if (ability == "LineageParameters") {
					permission = permissions.filter(function (el) {
						return el.ability.ability == "areas.view" || el.ability.ability == "shops.view" || el.ability.ability == "sections.view" || el.ability.ability == "departments.view" || el.ability.ability == "functionals.view" || el.ability.ability == "phases.view";
					});
				} else if (ability == "TypeParameters") {
					permission = permissions.filter(function (el) {
						return (
							el.ability.ability == "asset_types.view" ||
							el.ability.ability == "service_types.view" ||
							el.ability.ability == "spare_types.view" ||
							el.ability.ability == "activity_types.view" ||
							el.ability.ability == "data_source_types.view" ||
							el.ability.ability == "break_down_types.view" ||
							el.ability.ability == "accessory_types.view" ||
							el.ability.ability == "variable_types.view"
						);
					});
				}
				if (ability == "Attributes") {
					permission = permissions.filter(function (el) {
						return (
							el.ability.ability == "asset_attributes.view" ||
							el.ability.ability == "spare_attributes.view" ||
							el.ability.ability == "data_source_attributes.view" ||
							el.ability.ability == "variable_attributes.view" ||
							el.ability.ability == "service_attributes.view" ||
							el.ability.ability == "break_down_attributes.view" ||
							el.ability.ability == "activity_attributes.view"
						);
					});
				}
				if (ability == "MotorParameters") {
					permission = permissions.filter(function (el) {
						return (
							el.ability.ability == "voltages.view" ||
							el.ability.ability == "watt_ratings.view" ||
							el.ability.ability == "frames.view" ||
							el.ability.ability == "mountings.view" ||
							el.ability.ability == "makes.view" ||
							el.ability.ability == "speeds.view"
						);
					});
				}
				if (ability == "Masters") {
					permission = permissions.filter(function (el) {
						return el.ability.ability == "checks.view" || el.ability.ability == "spares.view" || el.ability.ability == "services.view" || el.ability.ability == "variables.view" || el.ability.ability == "data_sources.view";
					});
				}
				if (ability == "UserManagement") {
					permission = permissions.filter(function (el) {
						return el.ability.ability == "users.view" || el.ability.ability == "roles.view" || el.ability.ability == "permissions.view";
					});
				}
				if (ability == "Registers") {
					permission = permissions.filter(function (el) {
						return el.ability.ability == "health_check_registers.view" || el.ability.ability == "activity_registers.view" || el.ability.ability == "service_registers.view" || el.ability.ability == "check_registers.view" || el.ability.ability == "process_registers.view" || el.ability.ability == "break_down_registers.view" || el.ability.ability == "update_vendors.bulk";
					});
				}
				if (ability == "Review") {
					permission = permissions.filter(function (el) {
						return el.ability.ability == "asset_details.view" ||
							el.ability.ability == "spare_campaigns.view" ||
							el.ability.ability == "process_trends.view" ||
							el.ability.ability == "ppms_data.view";
					});
				}
				if (ability == "Reports") {
					permission = permissions.filter(function (el) {
						return el.ability.ability == "asset_reports.view" ||
							el.ability.ability == "job_pending_reports.view" ||
							el.ability.ability == "deviation_reports.view" ||
							el.ability.ability == "break_down_reports.view" ||
							el.ability.ability == "refractory_consumption_reports.view" ||
							el.ability.ability == "stl_daily_reports.view" || 
							el.ability.ability == "tlc_daily_reports.view";
					});
				}
				if (permission.length == 0) {
					return false;
				} else {
					return true;
				}
			}
		},
	},
};
</script>

<style scoped>
.sidebar-body .nav-sidebar {
	display: none;
	padding: 0 0 0px;
}

.sidebar-body .nav-label {
	font-size: 14px;
	text-transform: none;
	color: rgba(242, 237, 237, 0.963) !important;
}

.icn {
	margin-right: 8px;
	line-height: 1;
	font-size: 20px;
	width: 18px;
	opacity: 0.85;
}

.sidebar-body .nav-label.show1 {
	color: white !important;
}

.sidebar-body .nav-group.show .nav-label::after {
	content: "";
}

.sidebar-body {
	overflow-y: auto;
}

/* Custom scrollbar */
.sidebar-body::-webkit-scrollbar {
	width: 10px;
	/* Set the width of the scrollbar */
}

.sidebar-body::-webkit-scrollbar-track {
	/* background: #f1f1f1; */
	background: transparent;
}

.sidebar-body::-webkit-scrollbar-thumb {
	background: #888;
	/* Scrollbar color */
}

.sidebar-body::-webkit-scrollbar-thumb:hover {
	background: #555;
	/* Scrollbar color on hover */
}

.version-number {
	font-size: 12px;
	color: #5d6a77;
	margin-left: 5px;
	vertical-align: middle;
}

.nav-sidebar .nav-item .nav-link:hover {
	background: rgba(199, 193, 229, 0.2);
	color: blue !important;
	border-radius: 5px;
}

.nav-group .nav-label:hover {
	background: rgba(199, 193, 229, 0.2);
	color: blue !important;
	cursor: pointer;
	border-radius: 5px;
}

.pl-15 {
	padding-left: 40px !important;
}
</style>
