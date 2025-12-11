<template>
    <div class="col-12 mb-2" v-can="'asset_template_data_sources.view'">
        <div class="card card-one">
            <div class="card-header d-flex justify-content-between">
                <h6 class="card-title">Data Sources</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3 g-2" v-can="['asset_template_data_sources.create','asset_template_data_sources.update']">
                    <div class="col-md-3">
                        <label class="form-label">Template Zone <span class="text-danger"> *</span></label>
                        <MultiSelect
                            v-model="datasource.data_source_template_zones_obj"
                            filter
                            optionLabel="zone_name"
                            :options="asset_zones"
                            placeholder="Select Template Zone"
                            :maxSelectedLabels="3"
                            style="width: 100%; height: 37px;"
                            :style="errors?.data_source_template_zones ? error_style : ''"
                            :disabled="datasource.data_source_zone_read_only"
                        />
                        <span v-if="errors?.data_source_template_zones" class="invalid-feedback" style="display: block !important;">{{ errors?.data_source_template_zones[0] }}</span>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Data Source <span class="text-danger"> *</span></label>
                        <search
                            :class="{ 'is-invalid': errors?.data_source_id }"
                            :customClass="{ 'is-invalid': errors?.data_source_id }"
                            aria-describedby="basic-addon2"
                            aria-label="Select Data Source"
                            class="my-auto"
                            :initialize="datasource.data_source_id"
                            id="data_source_id"
                            label="data_source_name"
                            label2="data_source_code"
                            placeholder="Select Data Source"
                            :data="data_sources"
                            @input=" datasource1 => datasource.data_source_id = datasource1"
                            @selectsearch="getDatasourceAttribute(datasource.data_source_id)"
                        >
                        </search>
                        <span v-if="errors?.data_source_id" class="invalid-feedback">{{ errors?.data_source_id[0] }}</span>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Script <span class="text-danger"> *</span></label>
                        <select class="form-control form-control" v-model="datasource.script" :class="{'is-invalid':errors?.script}">
                            <option value="">Select Script</option>
                            <option value="Ladle Scanner">Ladle Scanner</option>
                            <option value="Torpedo Scanner">Torpedo Scanner</option>
                        </select>
                        <span v-if="errors?.script" class="invalid-feedback">{{ errors?.script[0] }}</span>
                    </div>

                    <div class="col-md-3" v-for="field, key in datasource.asset_datasource_attributes" :key="key">
                        <div v-if="field.data_source_attributes.length && field.data_source_attributes[0].field_type=='Text'">
                            <label class="form-label">{{field.data_source_attributes[0].display_name}}</label><span v-if="field.data_source_attributes[0].is_required" class="text-danger">*</span>
                            <input
                                type="text"
                                class="form-control"
                                :placeholder="'Enter '+ field.data_source_attributes[0].display_name"
                                :class="{'is-invalid': errors[field.data_source_attributes[0].display_name]}"
                                v-model="field.field_value"
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
                                v-model="field.field_value"
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
                                v-model="field.field_value"
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
                                v-model="field.field_value"
                                step="1"
                            />
                            <span v-if="errors[field.data_source_attributes[0].display_name]" class="invalid-feedback">
                                {{ errors[field.data_source_attributes[0].display_name][0] }}
                            </span>
                        </div>
                        <div v-if="field.data_source_attributes.length && field.data_source_attributes[0].field_type=='Dropdown'">
                            <label class="form-label">{{field.data_source_attributes[0].display_name}}</label><span v-if="field.data_source_attributes[0].is_required" class="text-danger">*</span>
                            <select class="form-control" :class="{'is-invalid': errors[field.data_source_attributes[0].display_name]}" v-model="field.field_value">
                                <option :value="field.field_value" v-if="field.field_value">{{field.field_value}}</option>
                                <option :value="field.field_value" v-else>Select {{field.data_source_attributes[0].display_name}}</option>
                                <option v-for="value, key in field.data_source_attributes[0].field_values.split(',')" :key="key" :value="value">{{value}}</option>
                            </select>
                            <span v-if="errors[field.data_source_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.data_source_attributes[0].display_name][0] }}</span>
                        </div>
                        <div v-if="field.data_source_attributes.length && field.data_source_attributes[0].field_type=='Color'">
                            <label class="form-label">{{ field.data_source_attributes[0].display_name }}<span v-if="field.data_source_attributes[0].is_required" class="text-danger">*</span></label>
                            <input type="color" class="form-control" v-model="field.field_value" style="height: 2.2rem;" />
                            <span v-if="errors[field.data_source_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.data_source_attributes[0].display_name][0] }}</span>
                        </div>

                        <div v-if="field.data_source_attributes.length && field.data_source_attributes[0].field_type=='List'">
                            <label class="form-label">{{field.data_source_attributes[0].display_name}}</label><span v-if="field.data_source_attributes[0].is_required" class="text-danger">*</span>
                            <select class="form-control" :class="{'is-invalid': errors[field.data_source_attributes[0].display_name]}" v-model="field.field_value">
                                <option :value="field.field_value" v-if="field.field_value">{{field.field_value}}</option>
                                <option :value="field.data_source_attributes[0].data_source_attribute_value?.field_value" v-if="field.data_source_attributes[0].data_source_attribute_value?.field_value">
                                    {{field.data_source_attributes[0].data_source_attribute_value?.field_value}}
                                </option>
                                <option :value="field.data_source_attributes[0].data_source_attribute_value?.field_value" v-else>Select {{field.data_source_attributes[0].display_name}}</option>
                                <option v-for="value, key in field.data_source_attributes[0].list_parameter?.field_values.split(',')" :key="key" :value="value.trim()">{{value.trim()}}</option>
                            </select>
                            <span v-if="errors[field.data_source_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.data_source_attributes[0].display_name][0] }}</span>
                        </div>
                    </div>

                    <div class="col-md-2 pt-4" v-if="datasource.asset_template_datasource_id">
                        <button v-can="'asset_template_data_sources.update'" class="btn btn-outline-success me-2" @click="updateDataSource()"><i class="ri-add-circle-line icon-hgt"></i> Update</button>
                    </div>
                    <div class="col-md-2 pt-4" v-else>
                        <button v-can="'asset_template_data_sources.create'" class="btn btn-outline-success me-2" @click="addDataSource()"><i class="ri-add-circle-line icon-hgt"></i> Add</button>
                    </div>
                </div>
                <input class="form-control form-control-sm mb-2" type="text" placeholder="Type keyword and press enter key" v-model="datasource_meta_service.search" @keypress.enter="searchDataSource()" />
                <div class="row g-2">
                    <div class="col-12">
                        <div class="table-responsive table-responsive-sm">
                            <table class="table table-sm text-nowrap table-striped table-bordered mb-0">
                                <thead>
                                    <tr class="">
                                        <th class="text-center">#</th>
                                        <th>
                                            Template Zone
                                            <span>
                                                <i v-if="datasource_meta_service.keyword=='template_zone_id' && datasource_meta_service.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="datasource_meta_service.keyword=='template_zone_id' && datasource_meta_service.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                            Data Source Type
                                            <span>
                                                <i v-if="datasource_meta_service.keyword=='data_source_type_id' && datasource_meta_service.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="datasource_meta_service.keyword=='data_source_type_id' && datasource_meta_service.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                            Data Source Code
                                            <span>
                                                <i v-if="datasource_meta_service.keyword=='data_source_code' && datasource_meta_service.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="datasource_meta_service.keyword=='data_source_code' && datasource_meta_service.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                            Data Source Name
                                            <span>
                                                <i v-if="datasource_meta_service.keyword=='data_source_name' && datasource_meta_service.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="datasource_meta_service.keyword=='data_source_name' && datasource_meta_service.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                            Script
                                            <span>
                                                <i v-if="datasource_meta_service.keyword=='script' && datasource_meta_service.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="datasource_meta_service.keyword=='script' && datasource_meta_service.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="asset_data_sources.length==0">
                                        <td colspan="7" class="text-center">No records found</td>
                                    </tr>
                                    <tr v-for="data_source, key in asset_data_sources" :key="key">
                                        <td class="text-center">{{(datasource_meta_service.page-1)*datasource_meta_service.per_page + key+1 }}</td>
                                        <td>{{ data_source.asset_zone?.zone_name }}</td>
                                        <td>{{ data_source.data_source?.data_source_type?.data_source_type_name }}</td>
                                        <td>{{ data_source?.data_source?.data_source_code }}</td>
                                        <td>{{ data_source?.data_source?.data_source_name }}</td>
                                        <td>{{ data_source?.script }}</td>
                                        <td class="text-center">
                                            <a v-can="'asset_template_data_sources.update'" href="javascript:void(0)" class="text-success me-2" @click="editDataSource(data_source)">
                                                <i class="ri-pencil-line fs-18 lh-1"></i>
                                            </a>
                                            <a href="javascript:void(0)" class="text-primary me-2" data-bs-toggle="modal" data-bs-target="#exampleModal" @click.prevent="viewAsset(data_source, 'Data Sources')">
                                                <i class="ri-eye-fill fs-18 lh-1"></i>
                                            </a>
                                            <a v-can="'asset_template_data_sources.delete'" href="javascript:void(0)" class="text-danger me-2" @click="deleteDataSource(data_source)">
                                                <i class="ri-delete-bin-6-line fs-18 lh-1"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <select class="form-select from-select-sm width-75" v-model="datasource_meta_service.per_page" @change="onPerPageChange('Data Sources')">
                        <option>5</option>
                        <option>10</option>
                        <option>15</option>
                        <option>20</option>
                        <option>25</option>
                        <option>30</option>
                    </select>
                    <span>Showing {{ datasource_meta_service.from }} to {{ datasource_meta_service.to }} of {{ datasource_meta_service.totalRows }} entries</span>
                    <Pagination :maxPage="datasource_meta_service.maxPage" :totalPages="datasource_meta_service.lastPage" :currentPage="datasource_meta_service.page" @pagechanged="onPageChange" :from="'Data Sources'" />
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
                                    <div v-if="field.spare_attributes.length && field.spare_attributes[0].field_type=='Text'">
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

                                    <div v-if="field.spare_attributes.length && field.spare_attributes[0].field_type=='Number'">
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

                                    <div v-if="field.spare_attributes.length && field.spare_attributes[0].field_type === 'Date'">
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

                                    <div v-if="field.spare_attributes.length && field.spare_attributes[0].field_type === 'Date&Time'">
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
                                    <div v-if="field.spare_attributes.length && field.spare_attributes[0].field_type=='Dropdown'">
                                        <label class="form-label">{{field.spare_attributes[0].display_name}}</label><span v-if="field.spare_attributes[0].is_required" class="text-danger">*</span>
                                        <select class="form-control" :class="{'is-invalid': errors[field.spare_attributes[0].display_name]}" :value="field.field_value" disabled="true">
                                            <option :value="field.field_value" v-if="field.field_value">{{field.field_value}}</option>
                                            <option :value="field.field_value" v-else>Select {{field.spare_attributes[0].display_name}}</option>
                                            <option v-for="value, key in field.spare_attributes[0].field_values.split(',')" :key="key" :value="value">{{value}}</option>
                                        </select>
                                        <span v-if="errors[field.spare_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.spare_attributes[0].display_name][0] }}</span>
                                    </div>
                                    <div v-if="field.spare_attributes.length && field.spare_attributes[0].field_type=='Color'">
                                        <label class="form-label">{{ field.spare_attributes[0].display_name }}<span v-if="field.spare_attributes[0].is_required" class="text-danger">*</span></label>
                                        <input type="color" class="form-control" :value="field.field_value" style="height: 2.2rem;" disabled="true" />
                                        <span v-if="errors[field.spare_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.spare_attributes[0].display_name][0] }}</span>
                                    </div>

                                    <div v-if="field.spare_attributes.length && field.spare_attributes[0].field_type=='List'">
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
                                    <div v-if="field.service_attributes.length && field.service_attributes[0].field_type=='Text'">
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

                                    <div v-if="field.service_attributes.length && field.service_attributes[0].field_type=='Number'">
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

                                    <div v-if="field.service_attributes.length && field.service_attributes[0].field_type === 'Date'">
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

                                    <div v-if="field.service_attributes.length && field.service_attributes[0].field_type === 'Date&Time'">
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
                                    <div v-if="field.service_attributes.length && field.service_attributes[0].field_type=='Dropdown'">
                                        <label class="form-label">{{field.service_attributes[0].display_name}}</label><span v-if="field.service_attributes[0].is_required" class="text-danger">*</span>
                                        <select class="form-control" :class="{'is-invalid': errors[field.service_attributes[0].display_name]}" :value="field.field_value" disabled="true">
                                            <option :value="field.field_value" v-if="field.field_value">{{field.field_value}}</option>
                                            <option :value="field.field_value" v-else>Select {{field.service_attributes[0].display_name}}</option>
                                            <option v-for="value, key in field.service_attributes[0].field_values.split(',')" :key="key" :value="value">{{value}}</option>
                                        </select>
                                        <span v-if="errors[field.service_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.service_attributes[0].display_name][0] }}</span>
                                    </div>
                                    <div v-if="field.service_attributes.length && field.service_attributes[0].field_type=='Color'">
                                        <label class="form-label">{{ field.service_attributes[0].display_name }}<span v-if="field.service_attributes[0].is_required" class="text-danger">*</span></label>
                                        <input type="color" class="form-control" :value="field.field_value" style="height: 2.2rem;" disabled="true" />
                                        <span v-if="errors[field.service_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.service_attributes[0].display_name][0] }}</span>
                                    </div>

                                    <div v-if="field.service_attributes.length && field.service_attributes[0].field_type=='List'">
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
    import axios from "axios";
    import Search from "@/components/Search.vue";
    import Pagination from "@/components/Pagination.vue";
    import MultiSelect from "primevue/multiselect";
    export default {
        name: "AssetTemplates.View",
        components: { Search, Pagination, MultiSelect },
        props: {
            asset_template_id: {
                type: Number,
                required: true,
            },
        },
        data() {
            return {
                datasource_meta_service: {
                    search: "",
                    order_by: "desc",
                    keyword: "data_source_id",
                    per_page: 10,
                    totalRows: 0,
                    page: 1,
                    lastPage: 1,
                    from: 1,
                    to: 1,
                    maxPage: 1,
                    trashed: false,
                    asset_template_id: "",
                    asset_type_id: "",
                },
                asset: {
                    asset_template_id: "",
                    plant_id: "",
                    template_code: "",
                    template_name: "",
                    asset_type_id: "",
                    section_id: "",
                    serial_no: "",
                    status: "",
                    QR_Code: "",
                },
                datasource: {
                    data_source_id: "",
                    data_source_code: "",
                    data_source_name: "",
                    asset_template_id: "",
                    template_zone_id: "",
                    data_source_template_zones_obj: [],
                    data_source_template_zones: [],
                    asset_template_datasource_id: "",
                    script: "",
                    asset_datasource_attributes: [],
                    initial_data_source_id: "",
                    initial_asset_datasource_attributes: [],
                    deleted_asset_datasource_values: [],
                    data_source_zone_read_only: false,
                },
                asset_spares: [],
                checks: [],
                asset_data_sources: [],
                template_services: [],
                template_spare_services: [],
                asset_zones: [],
                errors: [],
                status: true,
                asset_zone_status: false,
                asset_zone_status_datasources: false,
                asset_type: "",
                // on add other api should not call
                isValid: true,
                assets1: [],
                error_style: {
                    "border-color": "#dc3545",
                    "padding-right": "calc(1.5em + 0.812rem)",
                    "background-image": `url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e")`,
                    "background-repeat": "no-repeat",
                    "background-position": "right calc(0.375em + 0.203rem) center",
                    "background-size": "calc(0.75em + 0.406rem) calc(0.75em + 0.406rem)",
                },
            };
        },
        watch: {
            assets1() {
                this.assets1 = this.$store.getters.asset_templates;
                // You can also add any additional logic here if needed
            },

            "check.check_id": function () {
                let vm = this;
                let check_temp = vm.checks.filter((element) => {
                    return element.check_id == vm.check.check_id;
                });
                console.log("vm.check.template_check_status", vm.check.template_check_status);
                if (check_temp.length && !vm.check.template_check_status) {
                    vm.check.lcl = check_temp[0].lcl;
                    vm.check.ucl = check_temp[0].ucl;
                    vm.check.order = check_temp[0].order;
                    vm.check.default_value = check_temp[0].default_value;
                    vm.check.field_type = check_temp[0]?.field_type;
                    vm.check.field_values = check_temp[0]?.field_values;
                }
                if (vm.check.template_check_status) {
                    vm.check.field_type = check_temp[0]?.field_type;
                    vm.check.field_values = check_temp[0]?.field_values;
                }
            },
        },
        computed: {
            formattedTemplateServices() {
                return this.template_services.map((service) => ({
                    ...service,
                    displayName: `${service.zone_name} - ${service.service_name}`,
                }));
            },
            formattedTemplateSpareServices() {
                return this.template_spare_services.map((service) => ({
                    ...service,
                    displayName: `${service.zone_name} - ${service.service_name}`,
                }));
            },
        },
        mounted() {
            document.body.classList.add("sidebar-hide");
            this.getAssetTemplate();
        },
        methods: {
            getAssetTemplate() {
                let vm = this;
                vm.$store
                    .dispatch("post", { uri: "getAssetTemplate", data: { asset_template_id: vm.asset_template_id } })
                    .then((response) => {
                        vm.asset = response.data.data;
                        vm.asset_zones = response?.data?.data?.zone_name;
                        vm.getAssetDataSources();
                    })
                    .catch(function (error) {
                        vm.errors = error.response?.data?.errors;
                        vm.$store.dispatch("error", error.response?.data?.message);
                    });
            },
            getAssetDataSources() {
                let vm = this;
                let loader = vm.$loading.show();
                vm.datasource_meta_service.asset_template_id = vm.asset.asset_template_id;
                vm.datasource_meta_service.asset_type_id = vm.asset.asset_type_id;
                vm.$store
                    .dispatch("post", { uri: "paginateAssetTemplateDataSources", data: vm.datasource_meta_service })
                    .then((response) => {
                        loader.hide();
                        // console.log("RRRR--", response);
                        vm.data_sources = response.data.data_sources;
                        vm.asset_data_sources = response.data.paginate_data_sources;
                        vm.datasource_meta_service.totalRows = response.data.meta.total;
                        vm.datasource_meta_service.from = response.data.meta.from;
                        vm.datasource_meta_service.to = response.data.meta.to;
                        vm.datasource_meta_service.lastPage = response.data?.meta?.last_page;
                        vm.datasource_meta_service.maxPage = vm.datasource_meta_service.lastPage >= 3 ? 3 : vm.datasource_meta_service.lastPage;
                    })
                    .catch(function (error) {
                        loader.hide();
                        vm.errors = error.response?.data?.errors;
                        vm.$store.dispatch("error", error.response.data.message);
                    });
            },
            editDataSource(datasource) {
                this.datasource.data_source_id = datasource.data_source_id;
                this.datasource.asset_template_id = datasource.asset_template_id;
                this.datasource.asset_template_datasource_id = datasource.asset_template_datasource_id;
                this.datasource.data_source_code = datasource.data_source_code;
                this.datasource.data_source_name = datasource.data_source_name;
                this.datasource.data_source_template_zones = [];
                this.datasource.data_source_template_zones.push(datasource.template_zone_id);
                this.datasource.template_zone_id = datasource.template_zone_id;
                this.datasource.script = datasource.script;
                this.datasource.asset_datasource_attributes = datasource.asset_datasource_attributes;
                this.datasource.initial_data_source_id = datasource.data_source_id;
                this.datasource.initial_asset_datasource_attributes = datasource.asset_datasource_attributes;

                this.datasource.data_source_template_zones_obj = [];
                this.datasource.data_source_template_zones_obj.push({
                    asset_template_id: datasource.asset_zone.asset_template_id,
                    template_zone_id: datasource.asset_zone.template_zone_id,
                    zone_name: datasource.asset_zone.zone_name,
                    diameter: datasource.asset_zone.diameter,
                    height: datasource.asset_zone.height,
                    asset_spares: datasource.asset_zone.asset_spares,
                });
                this.datasource.data_source_zone_read_only = true;
            },
            validateFields(validation_type) {
                this.isValid = true;
                this.errors = {};
                if (validation_type == "Data Sources") {
                    for (const field of Object.values(this.datasource.asset_datasource_attributes)) {
                        if (field.data_source_attributes.length && field.data_source_attributes[0].is_required && !field.field_value) {
                            if (field.data_source_attributes[0].field_type === "Color") {
                                // Set default color if not provided
                                field.field_value = "#000000"; // Default to black
                            } else {
                                this.errors[field.data_source_attributes[0].display_name] = [`${field.data_source_attributes[0].display_name} is required`];
                                this.isValid = false;
                            }
                        }
                    }
                }
            },
            addDataSource() {
                let vm = this;
                vm.datasource.asset_template_id = vm.asset.asset_template_id;
                vm.datasource.data_source_template_zones = [];
                vm.datasource.data_source_template_zones_obj.map(function (ele) {
                    vm.datasource.data_source_template_zones.push(ele.template_zone_id);
                });
                let loader = vm.$loading.show();
                vm.validateFields("Data Sources");
                if (!vm.isValid) {
                    loader.hide();
                    return;
                }
                vm.$store
                    .dispatch("post", { uri: "addAssetTemplateDataSource", data: vm.datasource })
                    .then((response) => {
                        loader.hide();
                        vm.$store.dispatch("success", response.data.message);
                        vm.datasource.data_source_id = "";
                        vm.errors = [];
                        vm.datasource.template_zone_id = "";
                        vm.datasource.data_source_template_zones = [];
                        vm.datasource.script = "";
                        vm.datasource.asset_datasource_attributes = [];
                        vm.datasource.initial_data_source_id = "";
                        vm.datasource.initial_asset_datasource_attributes = [];
                        vm.datasource.deleted_asset_datasource_values = [];
                        vm.asset_zone_status_datasources = false;

                        vm.datasource.data_source_template_zones_obj = [];
                        vm.datasource.data_source_zone_read_only = false;

                        vm.getAssetDataSources();
                    })
                    .catch(function (error) {
                        loader.hide();
                        vm.errors = error.response.data.errors;
                        vm.$store.dispatch("error", error.response.data.message);
                    });
            },
            updateDataSource() {
                let vm = this;
                let loader = vm.$loading.show();
                vm.validateFields("Data Sources");
                if (!vm.isValid) {
                    loader.hide();
                    return;
                }
                if (vm.datasource.data_source_id != vm.datasource.initial_data_source_id) {
                    vm.datasource.initial_asset_datasource_attributes.map(function (ele) {
                        vm.datasource.deleted_asset_datasource_values.push(ele.template_datasource_value_id);
                    });
                }
                let uri = { uri: "updateAssetTemplateDataSource", data: vm.datasource };
                vm.$store
                    .dispatch("post", uri)
                    .then(function (response) {
                        loader.hide();
                        vm.$store.dispatch("success", response.data.message);
                        vm.datasource.data_source_id = "";
                        vm.datasource.template_zone_id = "";
                        vm.datasource.asset_template_datasource_id = "";
                        vm.datasource.data_source_template_zones = [];
                        vm.datasource.script = "";
                        vm.datasource.asset_datasource_attributes = [];
                        vm.datasource.initial_data_source_id = "";
                        vm.datasource.initial_asset_datasource_attributes = [];
                        vm.datasource.deleted_asset_datasource_values = [];
                        vm.asset_zone_status_datasources = false;
                        vm.datasource.data_source_template_zones_obj = [];
                        vm.datasource.data_source_zone_read_only = false;
                        vm.getAssetDataSources();
                    })
                    .catch(function (error) {
                        loader.hide();
                        vm.errors = error.response?.data?.errors;
                        vm.$store.dispatch("error", error?.response?.data?.message);
                    });
            },
            deleteDataSource(datasource) {
                const confirmDelete = confirm("Are you sure you want to delete it ?");
                if (confirmDelete) {
                    let vm = this;
                    let loader = vm.$loading.show();
                    vm.$store
                        .dispatch("post", { uri: "deleteAssetTemplateDataSource", data: datasource })
                        .then((response) => {
                            loader.hide();
                            vm.$store.dispatch("success", response.data.message);
                            vm.datasource.data_source_id = "";
                            vm.errors = [];
                            vm.getAssetDataSources();
                        })
                        .catch(function (error) {
                            loader.hide();
                            vm.errors = error.response.data.errors;
                            vm.$store.dispatch("error", error.response.data.message);
                        });
                }
            },
            searchDataSource() {
                let vm = this;
                vm.datasource_meta_service.page = 1;
                vm.getAssetDataSources();
            },
            onPageChange(page, from) {
                if (from == "Data Sources") {
                    this.datasource_meta_service.page = page;
                    this.getAssetDataSources();
                }
            },
            sort(field, from) {
                if (from == "Data Sources") {
                    this.datasource_meta_service.keyword = field;
                    this.datasource_meta_service.order_by = this.datasource_meta_service.order_by == "asc" ? "desc" : "asc";
                    this.getAssetDataSources();
                }
            },
            onPerPageChange(from) {
                if (from == "Data Sources") {
                    this.datasource_meta_service.page = 1;
                    this.getAssetDataSources();
                }
            },
            getDatasourceAttribute(data_source_id) {
                let vm = this;
                let loader = vm.$loading.show();
                vm.datasource.asset_datasource_attributes = [];
                vm.$store
                    .dispatch("post", { uri: "assetDataSourceAttributeValues", data: { data_source_id: data_source_id } })
                    .then((response) => {
                        loader.hide();
                        vm.datasource.asset_datasource_attributes = response.data.data;
                    })
                    .catch(function (error) {
                        loader.hide();
                        vm.errors = error.response.data.errors;
                        vm.$store.dispatch("error", error.response.data.message);
                    });
            },
            viewAsset(asset, from) {
                this.$store.commit("setAssetTemplates", asset);
                this.asset_type = from;
                this.assets1 = this.$store.getters.asset_templates;
            },
        },
    };
</script>
<style scoped>
.card-one {
    border-top-right-radius: 1px;
    border-top-left-radius: 1px;
}
</style>