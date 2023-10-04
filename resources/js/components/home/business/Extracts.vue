<template>
    <div class="split-screen">
        <div class="left-panel">
            <div class="text-left">
                <div v-for="(result, index) in resultStrings" :key="index">
                    <textarea class="form-control" rows="10" v-model="result.text"></textarea>
                </div>
            </div>
        </div>
        <div class="right-panel">
            <div class="text-right">
                <h6 class="modal-title">
                    <span>Trích xuất file PDF</span>
                </h6>
                <div class="d-inline-block" style="text-align: left;">
                    <b-form-file v-model="file" :state="Boolean(file)" placeholder="Choose a file or drop it here..."
                        drop-placeholder="Drop file here..."></b-form-file>
                    <div class="mt-3">{{ file ? file.name : '' }}</div>

                    <select v-model="selectedItem">
                        <option value="">Select an item</option>
                        <option v-for="item in items" :value="item">{{ item }}</option>
                    </select>
                    <select v-model="selectedItem">
                        <option value="">Select an item</option>
                        <option v-for="item in items" :value="item">{{ item }}</option>
                    </select>
                    <select v-model="selectedItem">
                        <option value="">Select an item</option>
                        <option v-for="item in items" :value="item">{{ item }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            file: null
        };
    }
};
</script>

<style>
.split-screen {
    display: flex;
    height: 100vh;
}

.left-panel {
    flex: 1;
    background-color: #f2f2f2;
    padding: 20px;
}

.right-panel {
    flex: 1;
    background-color: #eaeaea;
    padding: 20px;
}

.text-left {
    text-align: left;
}

.text-right {
    text-align: right;
}
</style>
