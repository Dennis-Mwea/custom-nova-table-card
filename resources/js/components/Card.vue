<template>
	<div class="custom-table">
		<card class="flex flex-col">
			<h1 v-if="title" class="py-6 text-90 font-normal text-2xl text-left pl-4">{{ title }}</h1>

			<div class="relative">
				<div class="relative overflow-hidden overflow-x-auto">
					<table cellpadding="0" cellspacing="0" class="table w-full" data-testid="resource-table">
						<thead v-if="header.length">
						<tr>
							<th v-for="head in header" :id="head.id" :class="head.class">
            <span class="cursor-pointer inline-flex items-center">
              {{ head.data }}
            </span>
							</th>
							<th></th>
						</tr>
						</thead>

						<tbody v-if="rows !== undefined && rows.length">
						<tr v-for="row in rows">
							<td v-for="column in row.columns" :id="column.id" :class="column.class" v-html="column.data"></td>
							<td class="td-fit text-right pr-6">
		            <span v-if="row.view || row.viewLink">
		              <router-link
				              :title="__('View')"
				              :to="row.viewLink"
				              class="cursor-pointer text-70 hover:text-primary mr-3">
		                <icon height="18" type="view" view-box="0 0 22 16" width="22"/>
		              </router-link>
		            </span>
							</td>
						</tr>
						</tbody>
						<tbody v-else>
						<tr>
							<td :colspan="8">
								<div class="relative">
									<div class="flex justify-center items-center px-6 py-8" style="">
										<div class="text-center">
											<svg class="mb-3" height="51" viewBox="0 0 65 51" width="65"
											     xmlns="http://www.w3.org/2000/svg">
												<path
														d="M56 40h2c.552285 0 1 .447715 1 1s-.447715 1-1 1h-2v2c0 .552285-.447715 1-1 1s-1-.447715-1-1v-2h-2c-.552285 0-1-.447715-1-1s.447715-1 1-1h2v-2c0-.552285.447715-1 1-1s1 .447715 1 1v2zm-5.364125-8H38v8h7.049375c.350333-3.528515 2.534789-6.517471 5.5865-8zm-5.5865 10H6c-3.313708 0-6-2.686292-6-6V6c0-3.313708 2.686292-6 6-6h44c3.313708 0 6 2.686292 6 6v25.049375C61.053323 31.5511 65 35.814652 65 41c0 5.522847-4.477153 10-10 10-5.185348 0-9.4489-3.946677-9.950625-9zM20 30h16v-8H20v8zm0 2v8h16v-8H20zm34-2v-8H38v8h16zM2 30h16v-8H2v8zm0 2v4c0 2.209139 1.790861 4 4 4h12v-8H2zm18-12h16v-8H20v8zm34 0v-8H38v8h16zM2 20h16v-8H2v8zm52-10V6c0-2.209139-1.790861-4-4-4H6C3.790861 2 2 3.790861 2 6v4h52zm1 39c4.418278 0 8-3.581722 8-8s-3.581722-8-8-8-8 3.581722-8 8 3.581722 8 8 8z"
														fill="#A8B9C5"></path>
											</svg>
											<h3 class="text-base text-80 font-normal mb-6">
												No data matched the given criteria.
											</h3>
										</div>
									</div>
									<div class="overflow-hidden overflow-x-auto relative"></div>
								</div>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<div class="bg-20 rounded-b">
					<pagination-links :is="paginationComponent"
					                  v-if="shouldShowPagination"
					                  :all-matching-resource-count="allMatchingResourceCount"
					                  :current-resource-count="paginator.data.length"
					                  :next="hasNextPage"
					                  :page="currentPage"
					                  :pages="totalPages"
					                  :per-page="perPage"
					                  :previous="hasPreviousPage"
					                  :resource-count-label="resourceCountLabel"
					                  @page="selectPage">
							<span v-if="resourceCountLabel"
							      :class="{ 'ml-auto': paginationComponent === 'pagination-links'}"
							      class="text-sm text-80 px-4">
		            {{ resourceCountLabel }}
		          </span>
					</pagination-links>
				</div>
			</div>
		</card>
	</div>
</template>

<script>
import {Paginatable, PerPageable} from 'laravel-nova'

export default {
	props: [
		'card',
	],

	mixins: [
		Paginatable,
		PerPageable,
	],

	computed: {
		resourceCountLabel() {
			const first = this.perPage * (this.currentPage - 1)

			return (
					this.paginator.data.length &&
					`${first + 1}-${first + this.paginator.data.length} ${this.__('of')} ${
							this.allMatchingResourceCount
					}`
			)
		},

		currentPerPage: function () {
			return this.perPage
		},

		totalPages() {
			return Math.ceil(this.allMatchingResourceCount / this.currentPerPage)
		},

		hasNextPage() {
			return Boolean(this.paginator && this.paginator.next_page_url)
		},

		hasPreviousPage() {
			return Boolean(this.paginator && this.paginator.prev_page_url)
		},

		paginationComponent() {
			return `pagination-${Nova.config['pagination'] || 'links'}`
		},

		shouldShowPagination: function () {
			return (
					this.paginator.data.length > 0
			)
		},
	},

	data: function () {
		return {
			rows: [],
			header: [],
			title: '',
			paginator: {},
			allMatchingResourceCount: 0,
			perPage: 0,
			currentPage: 1,
			config: [],
			resource: ''
		}
	},

	created: function () {
		const {header, rows, title, refresh, uuid, paginator, config} = this.card

		this.rows = rows
		this.header = header
		this.title = title
		this.config = config

		if (paginator !== undefined) {
			this.paginator = paginator
			this.perPage = paginator.per_page
			this.allMatchingResourceCount = paginator.total
		}

		if (refresh) {
			setInterval(function () {
				Nova.request().get('/nova-api/cards')
						.then(({data}) => {
							const card = data.find((value) => value.uuid === uuid)

							this.rows = card.rows
						})
			}, refresh * 1000)
		}
	},

	mounted: function () {

	},

	methods: {
		selectPage: function (page) {
			this.currentPage = page
			this.loadMore()
		},

		loadMore: function () {
			Nova.request().get(`/nova-vendor/custom-table${this.config.routes[0].url}`, {
				params: {
					page: this.currentPage,
				}
			}).then(response => {
				this.paginator = response.data.paginator
				this.rows = response.data.rows
				if (this.paginator !== undefined) {
					this.perPage = this.paginator.per_page
					this.allMatchingResourceCount = this.paginator.total
				}

				Nova.$emit('resources-loaded')
			}).catch(error => {
				console.error(error)
			})
		}
	}
}
</script>
