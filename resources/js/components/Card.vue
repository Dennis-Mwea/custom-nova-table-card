<template>
    <div class="custom-table">
        <card class="flex flex-col">
            <h1 class="py-6 text-90 font-normal text-2xl text-left pl-4" v-if="title">{{ title }}</h1>

            <table cellpadding="0" cellspacing="0" class="table w-full" data-testid="resource-table">
                <thead v-if="header.length">
                    <tr>
                        <th :class="head.class" :id="head.id" v-for="head in header">
                          <span class="cursor-pointer inline-flex items-center">
                            {{ head.data }}
                          </span>
                        </th>
                        <th></th>
                    </tr>
                </thead>

                <tbody v-if="rows.length">
                    <tr v-for="row in rows">
                        <td :class="column.class" :id="column.id" v-for="column in row.columns" v-html="column.data"></td>
                        <td class="td-fit text-right pr-6">
                          <span v-if="row.view">
                            <router-link
                                    :title="__('View')"
                                    :to="row.view"
                                    class="cursor-pointer text-70 hover:text-primary mr-3">
                              <icon height="18" type="view" view-box="0 0 22 16" width="22"/>
                            </router-link>
                          </span>
                        </td>
                    </tr>
                </tbody>

                <div class="relative empty-data" v-else>
                    <div class="flex justify-center items-center px-6 py-8" style="">
                        <div class="text-center">
                            <svg class="mb-3" height="51" viewBox="0 0 65 51" width="65"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M56 40h2c.552285 0 1 .447715 1 1s-.447715 1-1 1h-2v2c0 .552285-.447715 1-1 1s-1-.447715-1-1v-2h-2c-.552285 0-1-.447715-1-1s.447715-1 1-1h2v-2c0-.552285.447715-1 1-1s1 .447715 1 1v2zm-5.364125-8H38v8h7.049375c.350333-3.528515 2.534789-6.517471 5.5865-8zm-5.5865 10H6c-3.313708 0-6-2.686292-6-6V6c0-3.313708 2.686292-6 6-6h44c3.313708 0 6 2.686292 6 6v25.049375C61.053323 31.5511 65 35.814652 65 41c0 5.522847-4.477153 10-10 10-5.185348 0-9.4489-3.946677-9.950625-9zM20 30h16v-8H20v8zm0 2v8h16v-8H20zm34-2v-8H38v8h16zM2 30h16v-8H2v8zm0 2v4c0 2.209139 1.790861 4 4 4h12v-8H2zm18-12h16v-8H20v8zm34 0v-8H38v8h16zM2 20h16v-8H2v8zm52-10V6c0-2.209139-1.790861-4-4-4H6C3.790861 2 2 3.790861 2 6v4h52zm1 39c4.418278 0 8-3.581722 8-8s-3.581722-8-8-8-8 3.581722-8 8 3.581722 8 8 8z"
                                      fill="#A8B9C5"></path>
                            </svg>
                            <h3 class="text-base text-80 font-normal mb-6">
                                No data matched the given criteria.
                            </h3>
                        </div>
                    </div>
                    <div class="overflow-hidden overflow-x-auto relative"></div>
                </div>
            </table>
        </card>
    </div>
</template>

<script>
    export default {
        props: [
            'card',
        ],

        data: function () {
            return {
                rows: [],
                header: [],
                title: ''
            }
        },

        mounted: function () {
            const {header, rows, title, refresh, uuid} = this.card;

            this.rows = rows;
            this.header = header;
            this.title = title;

            if (refresh) {
                setInterval(function () {
                    Nova.request().get('/nova-api/cards')
                        .then(({data}) => {
                            const card = data.find((value) => value.uuid === uuid)

                            this.rows = card.rows
                        });
                }, refresh * 1000);
            }
        },
    }
</script>
