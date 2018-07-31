<template>
    <div class="AmiGrid">
        <table>
            <thead>
            <tr>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td></td>
                <td></td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        props: {
            data: {
                required: true,
                type: String,
                default: '[]',
            }
        },
        data() {
            return {
                headers: [],
                columns: [],
            };
        },
        mounted() {
            let list = JSON.parse(this.data) || [];
            this.headers = list.headers || [];
            this.columns = list.columns || [];
        }
    }
</script>

<style lang="scss">
    .AmiGrid {
        font-size: 45px;
    }
</style>
