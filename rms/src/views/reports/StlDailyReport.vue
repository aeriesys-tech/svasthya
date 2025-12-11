<template>
	<div>
		<!-- Header -->
		<div class="d-sm-flex align-items-center justify-content-between mb-2">
			<div>
				<ol class="breadcrumb fs-sm mb-1">
					<li class="breadcrumb-item" aria-current="page">
						<router-link to="/dashboard">Dashboard</router-link>
					</li>
					<li class="breadcrumb-item" aria-current="page">
						<a href="javascript:void(0)">Asset Reports</a>
					</li>
				</ol>
				<h4 class="main-title mb-0">STL Ladle Daily Report</h4>
			</div>
			<div class="d-flex">
				<div class="form-group me-2">
					<input type="date" class="form-control" v-model="meta.date" />
				</div>
				<button class="btn btn-primary me-2" @click="submit">
					<i class="ri-send-plane-line"></i> Submit
				</button>
				<button class="btn btn-primary" @click="download">
					<i class="ri-file-excel-line"></i> Download
				</button>
			</div>
		</div>

		<div v-if="loaded">
			<h6 class="text-center mb-3">
				Daily Report — {{ formatDateTime(report.start_date_time) }} to
				{{ formatDateTime(report.end_date_time) }}
			</h6>

			<!-- 1) MAIN TABLE -->
			<div class="card card-one mb-3">
				<div class="card-header py-2">
					<strong>STL Details</strong>
				</div>
				<div class="card-body">
					<div class="table-responsive table-responsive-sm">
						<table class="table table-sm text-nowrap table-striped table-bordered mb-0">
							<thead>
								<tr class="" style="background-color: #506fd9; color: white;">
									<th style="width:36%">Label</th>
									<th class="text-center" style="width:12%">Norm/SOP</th>
									<th class="text-center" style="width:12%">UOM</th>
									<th class="text-center" style="width:40%">{{ formatDate(report.date) }}</th>
								</tr>
							</thead>
							<tbody>
								<tr v-if="report_details.length === 0">
									<td colspan="4" class="text-center text-muted">No main report data available.</td>
								</tr>
								<tr v-for="(item, idx) in report_details" :key="'main-' + idx">
									<th>{{ item.label }}</th>
									<td class="text-center">{{ item.norm ?? '' }}</td>
									<td class="text-center">{{ item.uom ?? '' }}</td>
									<td class="text-center">{{ showValue(item.value) }}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- 2) DOWN STL TABLE -->
			<div class="card card-one mb-3">
				<div class="card-header py-2 d-flex align-items-center justify-content-between">
					<strong>Down STL Details</strong>
					<small class="text-muted">
						({{ downColumns.length }} STL{{ downColumns.length === 1 ? '' : 's' }})
					</small>
				</div>
				<div class="card-body">
					<div class="table-responsive table-responsive-sm">
						<table class="table table-sm text-nowrap table-striped table-bordered mb-0">
							<thead>
								<tr class="" style="background-color: #506fd9; color: white;">
									<th>Label</th>
									<th class="text-center">UOM</th>
									<th class="text-center" :colspan="Math.max(1, downColumns.length)">
										{{ formatDate(report.date) }}
									</th>
								</tr>
							</thead>
							<tbody>
								<tr v-if="downRowLabels.length === 0">
									<td :colspan="2 + Math.max(1, downColumns.length)" class="text-center text-muted">
										No down STL data available.
									</td>
								</tr>
								<tr v-for="(label, r) in downRowLabels" :key="'down-r-' + r">
									<th>{{ label }}</th>
									<td class="text-center">{{ firstUomFor(label) }}</td>
									<td v-for="(stl, c) in downParsedUnique" :key="'down-c-' + r + '-' + c"
										class="text-center">
										{{ valueFrom(stl.byLabel, label) }}
									</td>
									<td v-if="downColumns.length === 0" class="text-center">—</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- 3) LIVE STL TABLE -->
			<div class="card card-one mb-3">
				<div class="card-header py-2 d-flex align-items-center justify-content-between">
					<strong>Circulation STL Details</strong>
					<small class="text-muted">
						({{ liveColumns.length }} STL{{ liveColumns.length === 1 ? '' : 's' }})
					</small>
				</div>
				<div class="card-body">
					<div class="table-responsive table-responsive-sm">
						<table class="table table-sm text-nowrap table-striped table-bordered mb-0">
							<thead>
								<tr class="" style="background-color: #506fd9; color: white;">
									<th>Label</th>
									<th class="text-center">UOM</th>
									<th class="text-center" :colspan="Math.max(1, liveColumns.length)">
										{{ formatDate(report.date) }}
									</th>
								</tr>
							</thead>
							<tbody>
								<tr v-if="liveRowLabels.length === 0">
									<td :colspan="2 + Math.max(1, liveColumns.length)" class="text-center text-muted">
										No live STL data available.
									</td>
								</tr>
								<tr v-for="(label, r) in liveRowLabels" :key="'live-r-' + r">
									<th>{{ label }}</th>
									<td class="text-center">{{ firstLiveUomFor(label) }}</td>
									<td v-for="(stl, c) in liveParsedUnique" :key="'live-c-' + r + '-' + c"
										class="text-center">
										<span :class="getValueColor(valueFrom(stl.byLabel, label), label)">
											{{ valueFrom(stl.byLabel, label) }}
										</span>
									</td>
									<td v-if="liveColumns.length === 0" class="text-center">—</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

		</div>

		<div v-else class="text-center text-muted">Select a date and click Submit to load the report.</div>
	</div>
</template>

<script>
import moment from 'moment';

export default {
	data() {
		return {
			// ✅ default yesterday
			meta: { date: moment().subtract(1, 'day').format('YYYY-MM-DD') },
			report: {},
			report_details: [],
			down_stl_details: [],
			live_stl_details: [],
			loaded: false,
		};
	},

	methods: {
		formatDateTime(dt) { return dt ? moment(dt).format('DD-MMM-YY HH:mm') : ''; },
		formatDate(d) { return d ? moment(d).format('DD-MMM-YY') : ''; },
		showValue(v) { return (v === null || v === undefined) ? '' : String(v); },

		submit() {
			const loader = this.$loading.show();
			this.$store.dispatch('post', { uri: 'getSTLDailyReport', data: this.meta })
				.then(res => {
					loader.hide();
					const data = res.data || {};
					this.report = data;
					this.report_details = data?.stl_details ? JSON.parse(data.stl_details) : [];
					this.down_stl_details = Array.isArray(data?.down_stl_daily_asset_reports) ? data.down_stl_daily_asset_reports : [];
					this.live_stl_details = Array.isArray(data?.live_stl_daily_asset_reports) ? data.live_stl_daily_asset_reports : [];
					this.loaded = true;
				})
				.catch(err => {
					loader.hide();
					this.$store.dispatch('error', err?.response?.data?.message || 'Failed to load');
				});
		},

		download() {
			if (!this.loaded) {
				this.$store.dispatch('error', 'Please load the report data first.');
				return;
			}

			try {
				const workbook = this.generateExcelData();
				this.downloadExcelFile(workbook);
				this.$store.dispatch('success', 'Excel file downloaded successfully.');
			} catch (error) {
				console.error('Excel download error:', error);
				this.$store.dispatch('error', 'Failed to generate Excel file.');
			}
		},

		generateExcelData() {
			const workbook = {
				SheetNames: ['STL Daily Report'],
				Sheets: { 'STL Daily Report': {} }
			};

			let rowIndex = 1;
			const sheet = workbook.Sheets['STL Daily Report'];

			// Title (kept for structure; styling handled in HTML)
			sheet['A' + rowIndex] = { v: 'STL Ladle Daily Report', t: 's', s: { font: { bold: true, sz: 16 } } };
			rowIndex += 2;

			// Date range
			sheet['A' + rowIndex] = {
				v: `Report Period: ${this.formatDateTime(this.report.start_date_time)} to ${this.formatDateTime(this.report.end_date_time)}`,
				t: 's',
				s: { font: { bold: true } }
			};
			rowIndex += 2;

			// 1. Main
			sheet['A' + rowIndex] = { v: 'STL Details', t: 's', s: { font: { bold: true, sz: 14 } } };
			rowIndex += 1;

			sheet['A' + rowIndex] = { v: 'Label', t: 's' };
			sheet['B' + rowIndex] = { v: 'Norm/SOP', t: 's' };
			sheet['C' + rowIndex] = { v: 'UOM', t: 's' };
			sheet['D' + rowIndex] = { v: this.formatDate(this.report.date), t: 's' };
			rowIndex += 1;

			this.report_details.forEach(item => {
				sheet['A' + rowIndex] = { v: item.label || '', t: 's' };
				sheet['B' + rowIndex] = { v: item.norm || '', t: 's' };
				sheet['C' + rowIndex] = { v: item.uom || '', t: 's' };
				sheet['D' + rowIndex] = { v: this.showValue(item.value), t: 's' };
				rowIndex += 1;
			});

			rowIndex += 2;

			// 2. Down
			if (this.downParsedUnique.length > 0) {
				sheet['A' + rowIndex] = { v: `Down STL Details (${this.downColumns.length} STL${this.downColumns.length === 1 ? '' : 's'})`, t: 's', s: { font: { bold: true, sz: 14 } } };
				rowIndex += 1;

				sheet['A' + rowIndex] = { v: 'Label', t: 's' };
				sheet['B' + rowIndex] = { v: 'UOM', t: 's' };
				sheet['C' + rowIndex] = { v: this.formatDate(this.report.date), t: 's' };
				rowIndex += 1;

				this.downRowLabels.forEach(label => {
					sheet['A' + rowIndex] = { v: label, t: 's' };
					sheet['B' + rowIndex] = { v: this.firstUomFor(label), t: 's' };
					this.downParsedUnique.forEach((stl, index) => {
						const value = this.valueFrom(stl.byLabel, label);
						sheet[String.fromCharCode(67 + index) + rowIndex] = { v: value, t: 's' };
					});
					rowIndex += 1;
				});

				rowIndex += 2;
			}

			// 3. Live
			if (this.liveParsedUnique.length > 0) {
				sheet['A' + rowIndex] = { v: `Circulation STL Details (${this.liveColumns.length} STL${this.liveColumns.length === 1 ? '' : 's'})`, t: 's', s: { font: { bold: true, sz: 14 } } };
				rowIndex += 1;

				sheet['A' + rowIndex] = { v: 'Label', t: 's' };
				sheet['B' + rowIndex] = { v: 'UOM', t: 's' };
				sheet['C' + rowIndex] = { v: this.formatDate(this.report.date), t: 's' };
				rowIndex += 1;

				this.liveRowLabels.forEach(label => {
					sheet['A' + rowIndex] = { v: label, t: 's' };
					sheet['B' + rowIndex] = { v: this.firstLiveUomFor(label), t: 's' };

					this.liveParsedUnique.forEach((stl, index) => {
						const value = this.valueFrom(stl.byLabel, label);
						const cellRef = String.fromCharCode(67 + index) + rowIndex;
						const cell = { v: value, t: 's' };

						// Keep conditional coloring for values only (Excel-friendly)
						const colorLabels = ['SZ PP1', 'SZ PP2', 'MZ PP1', 'MZ PP2'];
						if (colorLabels.includes(label)) {
							const numValue = parseFloat(value);
							if (!isNaN(numValue)) {
								if (numValue < 35) cell.s = { font: { color: { rgb: 'D32F2F' } } };
								else if (numValue < 40) cell.s = { font: { color: { rgb: 'F57C00' } } };
								else cell.s = { font: { color: { rgb: '388E3C' } } };
							}
						}
						sheet[cellRef] = cell;
					});

					rowIndex += 1;
				});
			}

			// Column widths (used by our HTML renderer)
			sheet['!cols'] = [
				{ wch: 30 },
				{ wch: 15 },
				{ wch: 20 },
				{ wch: 15 },
			];

			return workbook;
		},

		downloadExcelFile(workbook) {
			const htmlContent = this.createExcelHTML(workbook);
			const blob = new Blob([htmlContent], {
				type: 'application/vnd.ms-excel'
			});

			const url = window.URL.createObjectURL(blob);
			const link = document.createElement('a');
			link.href = url;

			const dateStr = this.formatDate(this.report.date).replace(/-/g, '');
			link.download = `STL_Daily_Report_${dateStr}.xls`;

			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link);
			window.URL.revokeObjectURL(url);
		},

		createExcelHTML(workbook) {
			const sheet = workbook.Sheets[workbook.SheetNames[0]];
			const range = this.getRange(sheet);

			// NOTE: All text forced to dark colors, no gradients, high-contrast headers,
			// and the legend is REMOVED as requested.
			let html = `<!DOCTYPE html>
  <html>
  <head>
  <meta charset="UTF-8">
  <title>STL Daily Report</title>
  <style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
	font-family: 'Calibri', 'Arial', sans-serif;
	margin: 0;
	padding: 16px;
	background-color: #ffffff;
	line-height: 1.5;
	color: #1f2937 !important; /* dark gray */
  }
  table, tr, td, th { color: #1f2937 !important; }

  /* Container + header blocks */
  .container {
	max-width: 100%;
	margin: 0 auto;
	background: white;
	padding: 16px;
  }

  .header {
	text-align: center;
	margin-bottom: 16px;
	padding: 16px 12px;
	background: #f3f4f6; /* light gray */
	color: #1f2937 !important; /* dark text */
	border: 1px solid #e5e7eb;
	border-radius: 6px;
  }
  .header h1 {
	font-size: 22px;
	font-weight: 700;
	margin-bottom: 4px;
  }
  .header .subtitle {
	font-size: 13px;
	opacity: 0.9;
	font-weight: 500;
	color: #374151 !important;
  }

  .date-range {
	text-align: center;
	margin-bottom: 18px;
	padding: 10px;
	background: #ffffff;
	border: 1px solid #c7d2fe; /* indigo-200 */
	border-radius: 6px;
	font-weight: 600;
	color: #1f2937 !important;
	font-size: 13px;
  }

  /* Sections + tables */
  .section { margin-bottom: 24px; }

  .section-header {
	font-size: 15px;
	font-weight: 700;
	margin-bottom: 10px;
	padding: 10px 12px;
	background: #eef2ff;            /* soft indigo */
	border-left: 4px solid #6366f1;  /* indigo-500 */
	border: 1px solid #e5e7eb;
	border-radius: 4px;
	color: #1f2937 !important;
  }

  table {
	border-collapse: collapse;
	width: 100%;
	margin-bottom: 14px;
	background: white;
	border-radius: 6px;
	overflow: hidden;
	border: 1px solid #e5e7eb;
  }

  /* Make headers always dark text on light bg (Excel safe) */
  th {
	background: #eef2ff !important;   /* light indigo */
	color: #1f2937 !important;         /* dark text */
	font-weight: 700;
	text-align: center;
	padding: 10px 8px;
	border: 1px solid #e5e7eb;
	font-size: 13px;
	text-transform: uppercase;
	letter-spacing: 0.5px;
  }

  td {
	padding: 9px 8px;
	border: 1px solid #e5e7eb;
	text-align: center;
	vertical-align: middle;
	font-size: 12px;
	color: #1f2937 !important;
	font-weight: 500;
  }

  td.label {
	text-align: left;
	font-weight: 700;
	background: #f9fafb; /* light gray */
	color: #1f2937 !important;
	padding-left: 14px;
	border-right: 2px solid #e5e7eb;
  }

  /* Zebra rows (subtle, Excel safe) */
  tr:nth-child(even) td:not(.red):not(.yellow):not(.green) {
	background: #fbfdff; /* very light */
  }

  /* Conditional color cells (value-only) */
  .red    { color: #d32f2f !important; font-weight: 700; }
  .yellow { color: #f57c00 !important; font-weight: 700; }
  .green  { color: #388e3c !important; font-weight: 700; }

  .footer {
	margin-top: 18px;
	text-align: center;
	padding: 12px;
	background: #f9fafb;
	border: 1px solid #e5e7eb;
	border-radius: 6px;
	color: #6b7280;
	font-size: 12px;
	font-weight: 500;
  }

  /* Print */
  @media print {
	body { background: white; padding: 10px; }
	.container { box-shadow: none; }
  }
  </style>
  </head>
  <body>`;

			// Header (legend removed)
			html += `<div class="container">
	<div class="header">
	  <h1>STL Ladle Daily Report</h1>
	  <div class="subtitle">Refractory Management System - JSW Steel</div>
	</div>
	<div class="date-range">
	  📅 Report Period: ${this.escapeHTML(this.formatDateTime(this.report.start_date_time))} to ${this.escapeHTML(this.formatDateTime(this.report.end_date_time))}
	</div>`;

			// Render rows
			html += `<div class="section">`;

			for (let row = range.s.r; row <= range.e.r; row++) {
				const cellARef = String.fromCharCode(65) + (row + 1);
				const cellA = sheet[cellARef];

				// Section header rows (bold + size 14)
				if (cellA && cellA.s && cellA.s.font && cellA.s.font.bold && cellA.s.font.sz === 14) {
					if (row > range.s.r) {
						html += `</table></div><div class="section">`;
					}
					html += `<div class="section-header">${this.escapeHTML(cellA.v)}</div>`;
					html += `<table>`;
					continue;
				}

				// Skip big title row (handled in header)
				if (cellA && cellA.s && cellA.s.font && cellA.s.font.bold && cellA.s.font.sz === 16) {
					continue;
				}

				html += `<tr>`;

				for (let col = range.s.c; col <= range.e.c; col++) {
					const ref = String.fromCharCode(65 + col) + (row + 1);
					const cell = sheet[ref];

					if (cell) {
						const value = cell.v ?? '';
						let cellClass = '';
						let cellTag = 'td';

						// Conditional text color from cell.s.font.color
						if (cell.s && cell.s.font && cell.s.font.color) {
							const rgb = (cell.s.font.color.rgb || '').toUpperCase();
							if (rgb === 'D32F2F' || rgb === 'FF0000') cellClass = 'red';
							else if (rgb === 'F57C00' || rgb === 'FFA500') cellClass = 'yellow';
							else if (rgb === '388E3C' || rgb === '008000') cellClass = 'green';
						}

						// Header row detection: if first row of a section with strings "Label" / "UOM"
						// We’ll make any row whose first two cells are strings "Label" and "UOM" into THs.
						const firstCell = sheet['A' + (row + 1)];
						const secondCell = sheet['B' + (row + 1)];
						const isHeaderRow =
							(firstCell && typeof firstCell.v === 'string' && firstCell.v.toLowerCase() === 'label') &&
							(secondCell && typeof secondCell.v === 'string' && secondCell.v.toLowerCase() === 'uom');

						if (isHeaderRow) cellTag = 'th';
						else if (col === 0) cellClass = (cellClass ? cellClass + ' ' : '') + 'label';

						html += `<${cellTag} class="${cellClass}">${this.escapeHTML(value)}</${cellTag}>`;
					} else {
						html += `<td></td>`;
					}
				}

				html += `</tr>`;
			}

			html += `</table></div>`;

			html += `<div class="footer">
	  Generated on ${new Date().toLocaleString()} | RMS - Refractory Management System | JSW Steel
	</div>
  </div>
  </body>
  </html>`;

			return html;
		},

		escapeHTML(str) {
			if (typeof str !== 'string') return str;
			return str
				.replace(/&/g, '&amp;')
				.replace(/</g, '&lt;')
				.replace(/>/g, '&gt;')
				.replace(/"/g, '&quot;')
				.replace(/'/g, '&#39;');
		},

		getRange(sheet) {
			const range = { s: { c: 0, r: 0 }, e: { c: 0, r: 0 } };
			Object.keys(sheet).forEach(key => {
				if (key[0] === '!') return;
				const col = key.charCodeAt(0) - 65;
				const row = parseInt(key.slice(1)) - 1;
				if (range.s.c > col) range.s.c = col;
				if (range.s.r > row) range.s.r = row;
				if (range.e.c < col) range.e.c = col;
				if (range.e.r < row) range.e.r = row;
			});
			return range;
		},

		/* ---------- parsing & helpers ---------- */
		parseItem(item) {
			const arr = item?.stl_details ? JSON.parse(item.stl_details) : [];
			const byLabel = {};
			arr.forEach(r => { if (r?.label) byLabel[r.label] = r; });
			return { raw: arr, byLabel };
		},
		firstFrom(parsedList, label, key) {
			for (const p of parsedList) {
				const r = p.byLabel[label];
				if (r && r[key] !== undefined && r[key] !== null && r[key] !== '') {
					return key === 'value' ? this.showValue(r[key]) : r[key];
				}
			}
			return '';
		},
		valueFrom(byLabel, label) {
			const r = byLabel[label];
			return r ? this.showValue(r.value) : '';
		},

		getValueColor(value, label) {
			const colorLabels = ['SZ PP1', 'SZ PP2', 'MZ PP1', 'MZ PP2'];
			if (!colorLabels.includes(label)) return '';

			const numValue = parseFloat(value);
			if (isNaN(numValue)) return '';

			if (numValue < 35) return 'text-danger';
			else if (numValue < 40) return 'text-warning';
			else return 'text-success';
		},
	},

	computed: {
		/* -------- DOWN -------- */
		downParsed() { return this.down_stl_details.map(this.parseItem); },
		downParsedUnique() {
			const seen = new Set();
			const out = [];
			this.downParsed.forEach((p, i) => {
				const no = (p.byLabel['STL No']?.value || `STL ${i + 1}`).toString();
				if (!seen.has(no)) { seen.add(no); out.push(p); }
			});
			return out;
		},
		downColumns() {
			return this.downParsedUnique.map((p, i) => {
				const v = p.byLabel['STL No']?.value;
				return v ? String(v) : `STL ${i + 1}`;
			});
		},
		downRowLabels() {
			if (this.downParsedUnique.length === 0) return [];
			const seen = new Set(); const order = [];
			const pushAll = (arr) => arr.forEach(r => {
				if (r?.label && !seen.has(r.label)) { seen.add(r.label); order.push(r.label); }
			});
			pushAll(this.downParsedUnique[0].raw);
			for (let i = 1; i < this.downParsedUnique.length; i++) pushAll(this.downParsedUnique[i].raw);
			return order;
		},
		firstUomFor() { return (label) => this.firstFrom(this.downParsedUnique, label, 'uom'); },

		/* -------- LIVE -------- */
		liveParsed() { return this.live_stl_details.map(this.parseItem); },
		liveParsedUnique() {
			const seen = new Set();
			const out = [];
			this.liveParsed.forEach((p, i) => {
				const no = (p.byLabel['STL No']?.value || `STL ${i + 1}`).toString();
				if (!seen.has(no)) { seen.add(no); out.push(p); }
			});
			return out;
		},
		liveColumns() {
			return this.liveParsedUnique.map((p, i) => {
				const v = p.byLabel['STL No']?.value;
				return v ? String(v) : `STL ${i + 1}`;
			});
		},
		liveRowLabels() {
			if (this.liveParsedUnique.length === 0) return [];
			const seen = new Set(); const order = [];
			const pushAll = (arr) => arr.forEach(r => {
				if (r?.label && !seen.has(r.label)) { seen.add(r.label); order.push(r.label); }
			});
			pushAll(this.liveParsedUnique[0].raw);
			for (let i = 1; i < this.liveParsedUnique.length; i++) pushAll(this.liveParsedUnique[i].raw);
			return order;
		},
		firstLiveUomFor() { return (label) => this.firstFrom(this.liveParsedUnique, label, 'uom'); },
	},
};
</script>

<style scoped>
.table> :not(caption)>*>* {
	vertical-align: middle;
}

.bg-light {
	background-color: #f8f9fa !important;
}

/* Custom scrollbar for table responsiveness */
.table-responsive::-webkit-scrollbar {
	height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
	background: #f1f1f1;
	border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb {
	background: #c1c1c1;
	border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
	background: #a8a8a8;
}

/* Allow text wrapping for label columns only */
.table tbody th:first-child,
.table thead th:first-child {
	white-space: normal;
	min-width: 200px;
	max-width: 300px;
}
</style>
