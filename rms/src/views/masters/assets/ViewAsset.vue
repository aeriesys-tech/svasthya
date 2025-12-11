<template>
    <div class="">
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <div>
                <ol class="breadcrumb fs-sm mb-1">
                    <li class="breadcrumb-item" aria-current="page">
                        <router-link to="/dashboard">Dashboard</router-link>
                    </li>
                    <li class="breadcrumb-item"><router-link to="/assets">Assets</router-link></li>
                    <li class="breadcrumb-item active" aria-current="page">View</li>
                </ol>
                <h4 class="main-title mb-0">Assets</h4>
            </div>
            <router-link to="/assets" type="submit" class="btn btn-primary" style="float: right;"><i class="ri-list-check"></i> ASSETS</router-link>
        </div>
        <div class="row g-2">
            <div class="col-xl-3 mb-1">
                <div class="row">
                    <div class="col-12 mb-2">
                        <div class="card card-one">
                            <div class="card-header d-flex justify-content-between">
                                <h6 class="card-title">{{asset?.asset_code}}</h6>
                            </div>
                            <div class="card-body">
                                <nav class="nav nav-classic">
                                    <span class="nav-link text-dark"><span>Asset Code</span> <span class="badge text-dark" style="text-wrap: balance;">{{asset.asset_code}}</span></span>
                                    <span class="nav-link text-dark"><span>Asset Name</span> <span class="badge text-dark">{{asset.asset_name}}</span></span>
                                    <span class="nav-link text-dark"><span>Scanner Code</span> <span class="badge text-dark">{{asset?.scanner_code}}</span></span>
                                    <span class="nav-link text-dark"><span>PPMS Code</span> <span class="badge text-dark">{{asset?.ppms_code}}</span></span>
                                    <span class="nav-link text-dark"><span>Asset Type</span> <span class="badge text-dark">{{asset.asset_type?.asset_type_name}}</span></span>
                                    <span class="nav-link text-dark"><span>Asset Template</span> <span class="badge text-dark">{{asset.asset_template?.template_name}}</span></span>
                                    <span class="nav-link text-dark"><span>Latitude</span> <span class="badge text-dark">{{asset?.latitude}}</span></span>
                                    <span class="nav-link text-dark"><span>Longitude</span> <span class="badge text-dark">{{asset?.longitude}}</span></span>
                                    <span class="nav-link text-dark"><span>Radius</span> <span class="badge text-dark">{{asset?.radius}}</span></span>
                                    <span class="nav-link text-dark"><span>Phase</span> <span class="badge text-dark">{{asset?.phase?.phase_name}}</span></span>
                                    <span class="nav-link text-dark">
                                        <span>Department</span>
                                        <span class="" style="margin-left: auto; padding-right: 5px;">
                                            <ul class="list_style_none mb-0 text-end">
                                                <li v-if="asset?.asset_department_ids?.length === 0">No departments</li>
                                                <li v-for="department in asset.asset_department_ids" :key="department.id">
                                                    {{ department.department?.department_name }}
                                                </li>
                                            </ul>
                                        </span>
                                    </span>
                                    <span class="nav-link text-dark"><span>Functional</span> <span class="badge text-dark">{{asset.functional?.functional_name}}</span></span>
                                    <span class="nav-link text-dark" v-for="(zone, index) in asset.zone_name" :key="zone.asset_zone_id">
                                        <span>Zone {{ index + 1 }}</span>
                                        <span class="badge text-dark">{{ zone.zone_name }}</span>
                                    </span>
                                    <h6 class="nav-link text-dark d-flex justify-content-between" v-for="asset_attribute, key in asset.asset_attributes" :key="key">
                                        {{ asset_attribute.display_name}}
                                        <span v-if="asset_attribute.field_name == 'Color'" :style="getColor(asset_attribute)">{{ asset_attribute?.asset_attribute_value?.field_value }}</span>
                                        <span v-else>{{ asset_attribute?.asset_attribute_value?.field_value }}</span>
                                    </h6>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card card-one">
                            <div class="card-body text-center">
                                <div class="mb-3 mt-3">
                                    <img :src="asset?.QR_Code" class="img" style="width: 150px;" />
                                </div>
                                <h3 class="card-title">{{asset.asset_code}}</h3>
                                <p class="card-text">Please scan the QR Code to get the asset details...</p>
                                <div class="row g-2 g-sm-3 mb-3">
                                    <div class="col-sm">
                                        <button type="button" class="btn btn-primary" @click="downloadQR()"><i class="ri-download-2-line fs-18 lh-1"></i> Download QR</button>
                                    </div>
                                </div>
                            </div>
                            <!-- card-body -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- try -->
            <div class="col-xl-9 mb-2">
                <div class="card1 card-one1">
                    <div class="card-content1">
                        <ul class="nav nav-tabs nav-tabs-mb-icon nav-tabs-card">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" ref="services" href="#services" @click="openTab('services')">
                                    <em class="icon ni ni-web-development"></em>
                                    <span>Services</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" ref="checks" @click="openTab('checks')" href="#checks">
                                    <em class="icon ni ni-web-development"></em>
                                    <span>Checks</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" ref="spares" href="#spares" @click="openTab('spares')">
                                    <em class="icon ni ni-web-development"></em>
                                    <span>Spares</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" ref="variables" @click="openTab('variables')" href="#variables">
                                    <em class="icon ni ni-web-development"></em>
                                    <span>Variables</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" ref="data_sources" @click="openTab('data_sources')" href="#data_sources">
                                    <em class="icon ni ni-web-development"></em>
                                    <span>Data Sources</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" ref="accessories" @click="openTab('accessories')" href="#accessories">
                                    <em class="icon ni ni-web-development"></em>
                                    <span>Accessories</span>
                                </a>
                            </li>
                        </ul>
                        <div class="card-inner">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="services">
                                    <Services ref="services_tab" v-if="asset.asset_id && tab==='services'" :asset_id="asset.asset_id"></Services>
                                </div>
                                <div class="tab-pane fade" id="checks">
                                    <Checks ref="checks_tab" v-if="asset.asset_id && tab==='checks'" :asset_id="asset.asset_id"></Checks>
                                </div>
                                <div class="tab-pane fade" id="spares">
                                    <Spares ref="spares_tab" v-if="asset.asset_id && tab==='spares'" :asset_id="asset.asset_id"></Spares>
                                </div>
                                <div class="tab-pane fade" id="variables">
                                    <Variables ref="variables_tab" v-if="asset.asset_id && tab==='variables'" :asset_id="asset.asset_id"></Variables>
                                </div>
                                <div class="tab-pane fade" id="data_sources">
                                    <DataSources ref="data_sources_tab" v-if="asset.asset_id && tab==='data_sources'" :asset_id="asset.asset_id"></DataSources>
                                </div>
                                <div class="tab-pane fade" id="accessories">
                                    <Accessories ref="accessories_tab" v-if="asset.asset_id && tab==='accessories'" :asset_id="asset.asset_id"></Accessories>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{asset_type}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-12 mb-2">
                            <!-- Spare -->
                            <div class="row g-2" v-if="asset_type=='Spares'">
                                <div class="col-md-4" v-for="field, key in assets1?.asset_spare_attributes" :key="key">
                                    <div v-if="field.spare_attributes?.length && field.spare_attributes[0].field_type=='Text'">
                                        <label class="form-label">{{field.spare_attributes[0].display_name}}</label><span v-if="field.spare_attributes[0].is_required" class="text-danger">*</span>
                                        <input
                                            type="text"
                                            class="form-control"
                                            :placeholder="'Enter '+ field.spare_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.spare_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.spare_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.spare_attributes[0].display_name][0] }}</span>
                                    </div>

                                    <div v-if="field.spare_attributes?.length && field.spare_attributes[0].field_type=='Number'">
                                        <label class="form-label">{{field.spare_attributes[0].display_name}}</label><span v-if="field.spare_attributes[0].is_required" class="text-danger">*</span>
                                        <input
                                            type="number"
                                            class="form-control"
                                            min="0"
                                            oninput="validity.valid||(value='');"
                                            :placeholder="'Enter '+ field.spare_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.spare_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.spare_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.spare_attributes[0].display_name][0] }}</span>
                                    </div>

                                    <div v-if="field.spare_attributes?.length && field.spare_attributes[0].field_type === 'Date'">
                                        <label class="form-label">
                                            {{ field.spare_attributes[0].display_name }}
                                            <span v-if="field.spare_attributes[0].is_required" class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="date"
                                            class="form-control"
                                            :placeholder="'Enter ' + field.spare_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.spare_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.spare_attributes[0].display_name]" class="invalid-feedback">
                                            {{ errors[field.spare_attributes[0].display_name][0] }}
                                        </span>
                                    </div>

                                    <div v-if="field.spare_attributes?.length && field.spare_attributes[0].field_type === 'Date&Time'">
                                        <label class="form-label">
                                            {{ field.spare_attributes[0].display_name }}
                                            <span v-if="field.spare_attributes[0].is_required" class="text-danger">*</span>
                                        </label>

                                        <input
                                            type="datetime-local"
                                            class="form-control"
                                            :placeholder="'Enter ' + field.spare_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.spare_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            step="1"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.spare_attributes[0].display_name]" class="invalid-feedback">
                                            {{ errors[field.spare_attributes[0].display_name][0] }}
                                        </span>
                                    </div>
                                    <div v-if="field.spare_attributes?.length && field.spare_attributes[0].field_type=='Dropdown'">
                                        <label class="form-label">{{field.spare_attributes[0].display_name}}</label><span v-if="field.spare_attributes[0].is_required" class="text-danger">*</span>
                                        <select class="form-control" :class="{'is-invalid': errors[field.spare_attributes[0].display_name]}" :value="field.field_value" disabled="true">
                                            <option :value="field.field_value" v-if="field.field_value">{{field.field_value}}</option>
                                            <option :value="field.field_value" v-else>Select {{field.spare_attributes[0].display_name}}</option>
                                            <option v-for="value, key in field.spare_attributes[0].field_values.split(',')" :key="key" :value="value">{{value}}</option>
                                        </select>
                                        <span v-if="errors[field.spare_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.spare_attributes[0].display_name][0] }}</span>
                                    </div>
                                    <div v-if="field.spare_attributes?.length && field.spare_attributes[0].field_type=='Color'">
                                        <label class="form-label">{{ field.spare_attributes[0].display_name }}<span v-if="field.spare_attributes[0].is_required" class="text-danger">*</span></label>
                                        <input type="color" class="form-control" :value="field.field_value" style="height: 2.2rem;" disabled="true" />
                                        <span v-if="errors[field.spare_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.spare_attributes[0].display_name][0] }}</span>
                                    </div>

                                    <div v-if="field.spare_attributes?.length && field.spare_attributes[0].field_type=='List'">
                                        <label class="form-label">{{field.spare_attributes[0].display_name}}</label><span v-if="field.spare_attributes[0].is_required" class="text-danger">*</span>
                                        <select class="form-control" :class="{'is-invalid': errors[field.spare_attributes[0].display_name]}" :value="field.field_value" disabled="true">
                                            <option :value="field.spare_attributes[0].spare_attribute_value.field_value" v-if="field.spare_attributes[0].spare_attribute_value.field_value">
                                                {{field.spare_attributes[0].spare_attribute_value.field_value}}
                                            </option>
                                            <option :value="field.spare_attributes[0].spare_attribute_value.field_value" v-else>Select {{field.spare_attributes[0].display_name}}</option>
                                            <option v-for="value, key in field.spare_attributes[0].list_parameter?.field_values.split(',')" :key="key" :value="value">{{value}}</option>
                                        </select>
                                        <span v-if="errors[field.spare_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.spare_attributes[0].display_name][0] }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Service -->
                            <div class="row g-2" v-if="asset_type=='Services'">
                                <div class="col-md-4" v-for="field, key in assets1?.asset_service_attributes" :key="key">
                                    <div v-if="field.service_attributes?.length && field.service_attributes[0].field_type === 'Text'">
                                        <label class="form-label">{{field.service_attributes[0].display_name}}</label><span v-if="field.service_attributes[0].is_required" class="text-danger">*</span>
                                        <input
                                            type="text"
                                            class="form-control"
                                            :placeholder="'Enter '+ field.service_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.service_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.service_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.service_attributes[0].display_name][0] }}</span>
                                    </div>

                                    <div v-if="field.service_attributes?.length && field.service_attributes[0].field_type === 'Number'">
                                        <label class="form-label">{{field.service_attributes[0].display_name}}</label><span v-if="field.service_attributes[0].is_required" class="text-danger">*</span>
                                        <input
                                            type="number"
                                            class="form-control"
                                            min="0"
                                            oninput="validity.valid||(value='');"
                                            :placeholder="'Enter '+ field.service_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.service_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.service_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.service_attributes[0].display_name][0] }}</span>
                                    </div>

                                    <div v-if="field.service_attributes?.length && field.service_attributes[0].field_type === 'Date'">
                                        <label class="form-label">
                                            {{ field.service_attributes[0].display_name }}
                                            <span v-if="field.service_attributes[0].is_required" class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="date"
                                            class="form-control"
                                            :placeholder="'Enter ' + field.service_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.service_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.service_attributes[0].display_name]" class="invalid-feedback">
                                            {{ errors[field.service_attributes[0].display_name][0] }}
                                        </span>
                                    </div>

                                    <div v-if="field.service_attributes?.length && field.service_attributes[0].field_type === 'Date&Time'">
                                        <label class="form-label">
                                            {{ field.service_attributes[0].display_name }}
                                            <span v-if="field.service_attributes[0].is_required" class="text-danger">*</span>
                                        </label>

                                        <input
                                            type="datetime-local"
                                            class="form-control"
                                            :placeholder="'Enter ' + field.service_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.service_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            step="1"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.service_attributes[0].display_name]" class="invalid-feedback">
                                            {{ errors[field.service_attributes[0].display_name][0] }}
                                        </span>
                                    </div>
                                    <div v-if="field.service_attributes?.length && field.service_attributes[0].field_type === 'Dropdown'">
                                        <label class="form-label">{{field.service_attributes[0].display_name}}</label><span v-if="field.service_attributes[0].is_required" class="text-danger">*</span>
                                        <select class="form-control" :class="{'is-invalid': errors[field.service_attributes[0].display_name]}" :value="field.field_value" disabled="true">
                                            <option :value="field.field_value" v-if="field.field_value">{{field.field_value}}</option>
                                            <option :value="field.field_value" v-else>Select {{field.service_attributes[0].display_name}}</option>
                                            <option v-for="value, key in field.service_attributes[0].field_values.split(',')" :key="key" :value="value">{{value}}</option>
                                        </select>
                                        <span v-if="errors[field.service_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.service_attributes[0].display_name][0] }}</span>
                                    </div>
                                    <div v-if="field.service_attributes?.length && field.service_attributes[0].field_type === 'Color'">
                                        <label class="form-label">{{ field.service_attributes[0].display_name }}<span v-if="field.service_attributes[0].is_required" class="text-danger">*</span></label>
                                        <input type="color" class="form-control" :value="field.field_value" style="height: 2.2rem;" disabled="true" />
                                        <span v-if="errors[field.service_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.service_attributes[0].display_name][0] }}</span>
                                    </div>

                                    <div v-if="field.service_attributes?.length && field.service_attributes[0].field_type === 'List'">
                                        <label class="form-label">{{field.service_attributes[0].display_name}}</label><span v-if="field.service_attributes[0].is_required" class="text-danger">*</span>
                                        <select class="form-control" :class="{'is-invalid': errors[field.service_attributes[0].display_name]}" :value="field.field_value" disabled="true">
                                            <option :value="field.service_attributes[0].service_attribute_value?.field_value" v-if="field.service_attributes[0].service_attribute_value?.field_value">
                                                {{field.service_attributes[0].service_attribute_value?.field_value}}
                                            </option>
                                            <option :value="field.service_attributes[0].service_attribute_value?.field_value" v-else>Select {{field.service_attributes[0].display_name}}</option>
                                            <option v-for="value, key in field.service_attributes[0].list_parameter?.field_values.split(',')" :key="key" :value="value.trim()">{{value.trim()}}</option>
                                        </select>
                                        <span v-if="errors[field.service_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.service_attributes[0].display_name][0] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2" v-if="asset_type=='Variables'">
                                <div class="col-md-4" v-for="field, key in assets1?.asset_variable_attributes" :key="key">
                                    <div v-if="field.variable_attributes.length && field.variable_attributes[0].field_type=='Text'">
                                        <label class="form-label">{{field.variable_attributes[0].display_name}}</label><span v-if="field.variable_attributes[0].is_required" class="text-danger">*</span>
                                        <input
                                            type="text"
                                            class="form-control"
                                            :placeholder="'Enter '+ field.variable_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.variable_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.variable_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.variable_attributes[0].display_name][0] }}</span>
                                    </div>

                                    <div v-if="field.variable_attributes.length && field.variable_attributes[0].field_type=='Number'">
                                        <label class="form-label">{{field.variable_attributes[0].display_name}}</label><span v-if="field.variable_attributes[0].is_required" class="text-danger">*</span>
                                        <input
                                            type="number"
                                            class="form-control"
                                            min="0"
                                            oninput="validity.valid||(value='');"
                                            :placeholder="'Enter '+ field.variable_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.variable_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.variable_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.variable_attributes[0].display_name][0] }}</span>
                                    </div>

                                    <div v-if="field.variable_attributes.length && field.variable_attributes[0].field_type === 'Date'">
                                        <label class="form-label">
                                            {{ field.variable_attributes[0].display_name }}
                                            <span v-if="field.variable_attributes[0].is_required" class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="date"
                                            class="form-control"
                                            :placeholder="'Enter ' + field.variable_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.variable_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.variable_attributes[0].display_name]" class="invalid-feedback">
                                            {{ errors[field.variable_attributes[0].display_name][0] }}
                                        </span>
                                    </div>

                                    <div v-if="field.variable_attributes.length && field.variable_attributes[0].field_type === 'Date&Time'">
                                        <label class="form-label">
                                            {{ field.variable_attributes[0].display_name }}
                                            <span v-if="field.variable_attributes[0].is_required" class="text-danger">*</span>
                                        </label>

                                        <input
                                            type="datetime-local"
                                            class="form-control"
                                            :placeholder="'Enter ' + field.variable_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.variable_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            step="1"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.variable_attributes[0].display_name]" class="invalid-feedback">
                                            {{ errors[field.variable_attributes[0].display_name][0] }}
                                        </span>
                                    </div>
                                    <div v-if="field.variable_attributes.length && field.variable_attributes[0].field_type=='Dropdown'">
                                        <label class="form-label">{{field.variable_attributes[0].display_name}}</label><span v-if="field.variable_attributes[0].is_required" class="text-danger">*</span>
                                        <select class="form-control" :class="{'is-invalid': errors[field.variable_attributes[0].display_name]}" :value="field.field_value" disabled="true">
                                            <option :value="field.field_value" v-if="field.field_value">{{field.field_value}}</option>
                                            <option :value="field.field_value" v-else>Select {{field.variable_attributes[0].display_name}}</option>
                                            <option v-for="value, key in field.variable_attributes[0].field_values.split(',')" :key="key" :value="value">{{value}}</option>
                                        </select>
                                        <span v-if="errors[field.variable_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.variable_attributes[0].display_name][0] }}</span>
                                    </div>
                                    <div v-if="field.variable_attributes.length && field.variable_attributes[0].field_type=='Color'">
                                        <label class="form-label">{{ field.variable_attributes[0].display_name }}<span v-if="field.variable_attributes[0].is_required" class="text-danger">*</span></label>
                                        <input type="color" class="form-control" :value="field.field_value" style="height: 2.2rem;" disabled="true" />
                                        <span v-if="errors[field.variable_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.variable_attributes[0].display_name][0] }}</span>
                                    </div>

                                    <div v-if="field.variable_attributes.length && field.variable_attributes[0].field_type=='List'">
                                        <label class="form-label">{{field.variable_attributes[0].display_name}}</label><span v-if="field.variable_attributes[0].is_required" class="text-danger">*</span>
                                        <select class="form-control" :class="{'is-invalid': errors[field.variable_attributes[0].display_name]}" :value="field.field_value" disabled="true">
                                            <option :value="field.variable_attributes[0].variable_attribute_value?.field_value" v-if="field.variable_attributes[0].variable_attribute_value?.field_value">
                                                {{field.variable_attributes[0].variable_attribute_value?.field_value}}
                                            </option>
                                            <option :value="field.variable_attributes[0].variable_attribute_value?.field_value" v-else>Select {{field.variable_attributes[0].display_name}}</option>
                                            <option v-for="value, key in field.variable_attributes[0].list_parameter?.field_values.split(',')" :key="key" :value="value.trim()">{{value.trim()}}</option>
                                        </select>
                                        <span v-if="errors[field.variable_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.variable_attributes[0].display_name][0] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2" v-if="asset_type=='Data Sources'">
                                <div class="col-md-4" v-for="field, key in assets1?.asset_datasource_attributes" :key="key">
                                    <div v-if="field.data_source_attributes.length && field.data_source_attributes[0].field_type=='Text'">
                                        <label class="form-label">{{field.data_source_attributes[0].display_name}}</label><span v-if="field.data_source_attributes[0].is_required" class="text-danger">*</span>
                                        <input
                                            type="text"
                                            class="form-control"
                                            :placeholder="'Enter '+ field.data_source_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.data_source_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.data_source_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.data_source_attributes[0].display_name][0] }}</span>
                                    </div>

                                    <div v-if="field.data_source_attributes.length && field.data_source_attributes[0].field_type=='Number'">
                                        <label class="form-label">{{field.data_source_attributes[0].display_name}}</label><span v-if="field.data_source_attributes[0].is_required" class="text-danger">*</span>
                                        <input
                                            type="number"
                                            class="form-control"
                                            min="0"
                                            oninput="validity.valid||(value='');"
                                            :placeholder="'Enter '+ field.data_source_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.data_source_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.data_source_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.data_source_attributes[0].display_name][0] }}</span>
                                    </div>

                                    <div v-if="field.data_source_attributes.length && field.data_source_attributes[0].field_type === 'Date'">
                                        <label class="form-label">
                                            {{ field.data_source_attributes[0].display_name }}
                                            <span v-if="field.data_source_attributes[0].is_required" class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="date"
                                            class="form-control"
                                            :placeholder="'Enter ' + field.data_source_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.data_source_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.data_source_attributes[0].display_name]" class="invalid-feedback">
                                            {{ errors[field.data_source_attributes[0].display_name][0] }}
                                        </span>
                                    </div>

                                    <div v-if="field.data_source_attributes.length && field.data_source_attributes[0].field_type === 'Date&Time'">
                                        <label class="form-label">
                                            {{ field.data_source_attributes[0].display_name }}
                                            <span v-if="field.data_source_attributes[0].is_required" class="text-danger">*</span>
                                        </label>

                                        <input
                                            type="datetime-local"
                                            class="form-control"
                                            :placeholder="'Enter ' + field.data_source_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.data_source_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            step="1"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.data_source_attributes[0].display_name]" class="invalid-feedback">
                                            {{ errors[field.data_source_attributes[0].display_name][0] }}
                                        </span>
                                    </div>
                                    <div v-if="field.data_source_attributes.length && field.data_source_attributes[0].field_type=='Dropdown'">
                                        <label class="form-label">{{field.data_source_attributes[0].display_name}}</label><span v-if="field.data_source_attributes[0].is_required" class="text-danger">*</span>
                                        <select class="form-control" :class="{'is-invalid': errors[field.data_source_attributes[0].display_name]}" :value="field.field_value" disabled="true">
                                            <option :value="field.field_value" v-if="field.field_value">{{field.field_value}}</option>
                                            <option :value="field.field_value" v-else>Select {{field.data_source_attributes[0].display_name}}</option>
                                            <option v-for="value, key in field.data_source_attributes[0].field_values.split(',')" :key="key" :value="value">{{value}}</option>
                                        </select>
                                        <span v-if="errors[field.data_source_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.data_source_attributes[0].display_name][0] }}</span>
                                    </div>
                                    <div v-if="field.data_source_attributes.length && field.data_source_attributes[0].field_type=='Color'">
                                        <label class="form-label">{{ field.data_source_attributes[0].display_name }}<span v-if="field.data_source_attributes[0].is_required" class="text-danger">*</span></label>
                                        <input type="color" class="form-control" :value="field.field_value" style="height: 2.2rem;" disabled="true" />
                                        <span v-if="errors[field.data_source_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.data_source_attributes[0].display_name][0] }}</span>
                                    </div>

                                    <div v-if="field.data_source_attributes.length && field.data_source_attributes[0].field_type=='List'">
                                        <label class="form-label">{{field.data_source_attributes[0].display_name}}</label><span v-if="field.data_source_attributes[0].is_required" class="text-danger">*</span>
                                        <select class="form-control" :class="{'is-invalid': errors[field.data_source_attributes[0].display_name]}" :value="field.field_value" disabled="true">
                                            <option :value="field.data_source_attributes[0].data_source_attribute_value?.field_value" v-if="field.data_source_attributes[0].data_source_attribute_value?.field_value">
                                                {{field.data_source_attributes[0].data_source_attribute_value?.field_value}}
                                            </option>
                                            <option :value="field.data_source_attributes[0].data_source_attribute_value?.field_value" v-else>Select {{field.data_source_attributes[0].display_name}}</option>
                                            <option v-for="value, key in field.data_source_attributes[0].list_parameter?.field_values.split(',')" :key="key" :value="value.trim()">{{value.trim()}}</option>
                                        </select>
                                        <span v-if="errors[field.data_source_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.data_source_attributes[0].display_name][0] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import Services from "@/components/AssetView/Services.vue";
    import DataSources from "@/components/AssetView/DataSources.vue";
    import Variables from "@/components/AssetView/Variables.vue";
    import Accessories from "@/components/AssetView/Accessories.vue";
    import Checks from "@/components/AssetView/Checks.vue";
    import Spares from "@/components/AssetView/Spares.vue";
    export default {
        name: "Assets.View",
        components: { DataSources, Accessories, Services, Variables, Checks, Spares },
        data() {
            return {
                tab: "services",
                asset: {
                    asset_id: "",
                    plant_id: "",
                    asset_code: "",
                    asset_name: "",
                    asset_type_id: "",
                    section_id: "",
                    serial_no: "",
                    status: "",
                    QR_Code: "",
                },
                asset_zones: [],
            };
        },
        beforeRouteEnter(to, from, next) {
            next((vm) => {
                document.body.classList.add("sidebar-hide");
                vm.asset.asset_id = to.params.asset_id;
                let uri = { uri: "getAsset", data: { asset_id: to.params.asset_id } };
                vm.$store
                    .dispatch("post", uri)
                    .then(function (response) {
                        vm.asset = response.data.asset;
                        vm.asset_zones = response?.data?.asset?.zone_name;
                        vm.asset.QR_Code = response?.data?.QRCode;
                    })
                    .catch(function (error) {
                        console.log(error);
                        vm.errors = error.response.data.errors;
                        vm.$store.dispatch("error", error.response.data.message);
                    });
            });
        },
        mounted() {
            document.body.classList.add("sidebar-hide");
            this.getSettings();
        },
        methods: {
            getSettings() {
                let vm = this;
                vm.openTab(vm.tab);
            },
            openTab(tab) {
                let vm = this;
                vm.tab = tab;
                {
                    if (vm.tab == "services") {
                        vm.$refs.services_tab;
                    }
                    if (vm.tab == "checks") {
                        vm.$refs.checks_tab;
                    }
                    if (vm.tab == "spares") {
                        vm.$refs.spares_tab;
                    }
                    if (vm.tab == "variables") {
                        vm.$refs.variables_tab;
                    }
                    if (vm.tab == "data_sources") {
                        vm.$refs.data_sources_tab;
                    }
                    if (vm.tab == "accessories") {
                        vm.$refs.accessories_tab;
                    }
                }
            },

            downloadQR() {
                let vm = this;
                window.open(vm.$store.state.apiUrl + "downloadAssetQRCode?asset_code=" + vm.asset.asset_code);
            },
        },
    };
</script>
<style scoped>
    .list_style_none {
        list-style: none;
    }
    .department-badge {
        display: inline-block;
        max-width: 200px;
        overflow: hidden;
        white-space: wrap;
    }
    .badge {
        /* color: #6e7985; */
        font-size: 14px;
        opacity: inherit;
        margin-left: auto;
    }

    .border-left {
        border-top-left-radius: 0px;
        border-bottom-left-radius: 0px;
    }

    /* multi seleect css */

    .dropdown {
        position: relative;
        cursor: pointer;
    }
    .multiselect {
        position: relative;
    }
    .multiselect ul {
        border: 1px solid #ddd;
        border-top: 0;
        border-radius: 0 0 3px 3px;
        left: 0px;
        padding: 8px 8px;
        top: -0.1rem;
        width: 100%;
        list-style: none;
        max-height: 150px;
        overflow: auto;
        background: white;
    }
    .overselect {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
    }

    .nav-tabs.nav-tabs-card {
        display: flex;
        width: 100%;
    }

    .nav-tabs.nav-tabs-card .nav-item {
        flex: 1;
        text-align: center;
        background-color:white;
    }

    .nav-tabs.nav-tabs-card .nav-link {
        width: 100%;
        padding: 10px;
        /* background-color: yellow; */
    }
    .nav-tabs .nav-link.active, .nav-tabs .nav-item.show .nav-link {
    color: blue;
    font-weight: 500;
    background-color: #ebf0f7;
}
.nav-link {
    color: black;
    box-shadow: 0 1px 2px rgba(204, 210, 218, 0.07), 0 2px 4px rgba(204, 210, 218, 0.07), 0 4px 8px rgba(204, 210, 218, 0.07), 0 8px 16px rgba(204, 210, 218, 0.07), 0 16px 32px rgba(204, 210, 218, 0.07), 0 32px 64px rgba(204, 210, 218, 0.07);

}

</style>
