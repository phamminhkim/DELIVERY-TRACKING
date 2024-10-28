<template>
  <Pie :chart-options="chartOptions" :chart-data="chartData" :chart-id="chartId" :dataset-id-key="datasetIdKey"
    :plugins="plugins" :css-classes="cssClasses" :styles="styles"/>
</template>

<script>
import { Pie } from "vue-chartjs/legacy";

import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  CategoryScale,
} from "chart.js";

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale);

export default {
  name: "PieChart",
  components: {
    Pie,
  },
  props: {
    chartId: {
      type: String,
      default: "pie-chart",
    },
    datasetIdKey: {
      type: String,
      default: "label",
    },
    width: {
      type: Number,
      default: 400,
    },
    height: {
      type: Number,
      default: 300,
    },
    cssClasses: {
      default: "",
      type: String,
    },
    styles: {
      type: Object,
      default: () => { },
    },
    plugins: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      chartData: {
        labels: [
          "Đã đồng bộ",
          "Chưa đồng bộ"
        ],
        datasets: [
          {
            backgroundColor: [
              "rgb(0 134 139)",
              "rgb(0 139 0)",
             
            ],

            data: [40, 20],
          },
        ],
      },
      chartOptions: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            display: true,
            position: "right",
          },
          title: {
            display: false,
            text: "TRẠNG THÁI ĐƠN HÀNG",
            // color: "rgb(255 255 224)",
            color: "black",
            align: "right",
            padding: 20,
            position: "top",
            font: {
              weight: "bold",
            },
          },
          datalabels: {
            anchor: 'center', // Vị trí của nhãn (dưới cùng cột)  
            align: 'center', // Căn chỉnh nhãn (dưới cùng cột)  
            color: 'white', // Màu văn bản  
            font: {
              size: 12, // Kích thước font
              lineHeight: 1.2, // Chiều cao dòng
              weight: 'bold',
            },
          },
        },
      },
    };
  },
};
</script>