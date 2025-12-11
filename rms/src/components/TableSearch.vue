<template>
    <div :class="[isOpen ? 'search search-open' : 'search']">
        <div class="search-inner">
            <input class="form-control search-selected" ref="toggle" :tabindex="tabindex" :value="selectedText" :class="customClass" :disabled="disabled" @click="onToggle" @keydown="onKey" :style="style" />
            <transition name="fade" mode="out-in">
                <div class="search-dropdown" v-if="isOpen">
                    <div class="search-input_wrap" :style="style">
                        <input
                            type="text"
                            class="form-control search-input"
                            autocomplete="off"
                            placeholder="Search..."
                            ref="search"
                            @blur="onBlur"
                            @input="onSearch"
                            @keydown.esc="onEsc"
                            @keydown.up="onUpKey"
                            @keydown.down="onDownKey"
                            @keydown.enter="onEnterKey"
                        />
                    </div>
                    <table class="table table-responsive-sm table-bordered table-striped table-sm search-list" v-if="results?.length">
                        <thead>
                            <tr>
                                <th v-for="column in columns" :key="column.field">{{ column.header }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                class="search-item"
                                v-for="(result, index) in results"
                                @mousedown.prevent="select(result)"
                                @mouseover.prevent="onMouse(index)"
                                :class="['search-link', selectIndex === index ? 'search-active' : '']"
                                :key="index"
                            >
                                <td v-for="column in columns" :key="column.field">
                                    {{ getFieldValue(result, column.field) || 'N/A' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </transition>
        </div>
        <span v-if="hasError" class="invalid-feedback">{{ errorMessage }}</span>
    </div>
</template>

<script>
    export default {
        name: "SpareSearch",
        props: {
            customClass: { type: [Object, String], default: "" },
            initialize: { type: [String, Number], default: null },
            id: { type: String, default: null },
            placeholder: { type: String, default: "Select an option" },
            data: { type: Array, default: () => [] },
            tabindex: { type: Number, default: 0 },
            disabled: { type: Boolean, default: false },
            style: { type: Object, default: null },
            columns: {
                type: Array,
                default: () => [{ header: "Name", field: "name" }],
            },
        },
        data() {
            return {
                selectIndex: -1,
                isOpen: false,
                search: "",
                results: [],
                query: { search: "" },
            };
        },
        computed: {
            hasError() {
                return !!this.customClass["is-invalid"];
            },
            errorMessage() {
                return this.hasError ? "This field is required" : "";
            },
            selectedText() {
                if (this.initialize && this.data) {
                    const result = this.data.find((el) => el[this.id] === this.initialize);
                    if (result) {
                        return this.columns.map((column) => this.getFieldValue(result, column.field) || "N/A").join(" :: ");
                    }
                }
                return this.placeholder;
            },
        },
        methods: {
            focus() {
                this.$refs.toggle.focus();
            },
            onToggle() {
                this.isOpen = !this.isOpen;
                if (this.isOpen) {
                    this.open();
                }
            },
            onKey(e) {
                const keyCode = e.keyCode || e.which;
                if (!e.shiftKey && keyCode !== 9 && !this.isOpen) {
                    this.open();
                }
            },
            open() {
                this.fetchData("");
                this.isOpen = true;
                this.$nextTick(() => {
                    this.$refs.search.focus();
                });
            },
            fetchData(query) {
                query = query.toLowerCase();
                this.results = query
                    ? this.data.filter((el) =>
                          this.columns.some((column) =>
                              String(this.getFieldValue(el, column.field) || "")
                                  .toLowerCase()
                                  .includes(query)
                          )
                      )
                    : this.data;
            },
            getFieldValue(obj, field) {
                if (!obj || !field) return null;
                if (field.includes(".")) {
                    return field.split(".").reduce((o, key) => (o && o[key] !== undefined ? o[key] : null), obj);
                }
                return obj[field];
            },
            onBlur() {
                this.close();
            },
            onEsc() {
                this.close();
            },
            close() {
                this.results = [];
                this.isOpen = false;
                this.search = "";
                this.selectIndex = -1;
            },
            onSearch(e) {
                this.selectIndex = 0;
                this.fetchData(e.target.value);
            },
            onUpKey() {
                if (this.selectIndex > 0) {
                    this.selectIndex--;
                }
            },
            onDownKey() {
                if (this.results.length - 1 > this.selectIndex) {
                    this.selectIndex++;
                }
            },
            onEnterKey() {
                const found = this.results[this.selectIndex];
                if (found) {
                    this.select(found);
                }
            },
            select(result) {
                this.$emit("input", result[this.id]);
                this.$emit("selected", result[this.id]);
                this.$emit("selectsearch", result);
                this.close();
            },
            onMouse(index) {
                this.selectIndex = index;
            },
        },
    };
</script>

<style scoped>
    .search {
        position: relative;
        display: block;
        background: #fff;
    }
    .search-open {
        border-bottom: 0;
    }
    .search-open .form-control {
        background: #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        border: 1px solid #f2f2f2;
    }
    .search-inner {
        position: relative;
    }
    .search-selected {
        cursor: pointer;
        user-select: none;
        display: flex !important;
        justify-content: space-between;
    }
    .search-dropdown {
        width: auto;
        min-width: 300px;
        max-width: 600px;
        position: absolute;
        z-index: 999 !important;
        padding: 5px;
        background: #fff;
        border-right: 1px solid #f2f2f2;
        border-left: 1px solid #f2f2f2;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        overflow-y: scroll !important;
        max-height: 150px !important;
    }
    .search-input {
        line-height: 13px;
        font-size: 13px;
        background: #fafafa;
        border: none;
        min-height: 10px !important;
        border-radius: 1px;
        box-shadow: inset 0 1px 1px 0 rgba(0, 0, 0, 0.1);
        padding: 4px 4px;
        width: 100%;
        display: block;
    }
    .search-input:focus {
        outline-style: dotted;
        outline-width: 1px;
        outline-offset: 1px;
    }
    .search-input_wrap {
        position: relative;
    }
    .search-list {
        margin: 0;
        padding: 0;
        width: 100%;
        table-layout: auto;
    }
    .search-list th,
    .search-list td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        padding: 8px;
    }
    .search-list th {
        background-color: #f8f9fa;
        font-weight: bold;
        position: sticky;
        top: 0;
        z-index: 1;
    }
    .search-link {
        cursor: pointer;
        background: #fff;
    }
    .search-active {
        background: #3aa3e3 !important;
        color: #fff;
    }
    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875rem;
    }
</style>
