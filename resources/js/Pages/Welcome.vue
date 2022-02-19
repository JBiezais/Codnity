<template>
    <div class="w-4/5 mx-auto my-24">
        <table id="myTable" class="display">
            <thead>
            <tr>
                <th>Scraped Id</th>
                <th>Title</th>
                <th>Link</th>
                <th>Points</th>
                <th>Created date</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    name: "Welcome",
    props:{
        data: Array,
    },
    mounted(){
        $('#myTable').DataTable({
            "data": this.data,
            "columns": [
                {"data": "scrapedId"},
                {"data": "title"},
                {"data": "link"},
                {"data": "points"},
                {"data": "created_date"},
            ]
        });
    }
}
</script>

