<template>
  <Pie :chart-options="chartOptions" :chart-data="chartDataComputed" :chart-id="chartId" :dataset-id-key="datasetIdKey"
    />
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
      default: 400,
    },
    data_dashboard_pie: {
      type: Object,
      default: () => {
        return {
          labels: [],
          datasets: [],
        };
      },
    },
  },
  data() {
    return {
      chartData: {
        labels: [
          "Aeon",
          "Bách Hóa Xanh",
          "BigC",
          "Coopmart",
          "Đơn Bổ Sung",
          "Emart",
          "Fujimart",
          "Lotte",
          "Mega",
        ],
        datasets: [
          {
            backgroundColor: [
              "rgb(0 134 139)",
              "rgb(0 139 0)",
              "rgb(205 85 85)",
              "rgb(205 155 155)",
              "rgb(205 205 0)",
              "rgb(205 149 12)",
              "rgb(24 116 205)",
              "rgb(255 20 147)",
              "rgb(180 82 205)",
            ],

            data: [40, 20, 80, 10, 20, 50, 30, 70, 80],
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
            text: "PO NHÓM KHÁCH HÀNG",
            // color: "rgb(255 255 224)",
            color: "black",
            font: {
              weight: "bold",
            },
          },
          datalabels: {
            anchor: 'center', // Vị trí của nhãn (dưới cùng cột)  
            align: 'center', // Căn chỉnh nhãn (dưới cùng cột)  
            color: 'black', // Màu văn bản  
            font: {
              size: 12, // Kích thước font
              lineHeight: 1.2, // Chiều cao dòng
              weight: 'bold',
            },
          },
        },
      },
      background_color: [
        {
          label: "Winmart",
          backgroundColor: "rgb(0 134 139)",
        },
        {
          label: "Mega",
          backgroundColor: "rgb(0 139 0)",
        },
        {
          label: "Lotte",
          backgroundColor: "rgb(205 85 85)",
        },
        {
          label: "BigC",
          backgroundColor: "rgb(205 155 155)",
        },
        {
          label: "Aeon",
          backgroundColor: "rgb(205 205 0)",
        },
        {
          label: "Emart",
          backgroundColor: "rgb(205 149 12)",
        },
        {
          label: "Fujimart",
          backgroundColor: "rgb(24 116 205)",
        },
        {
          label: "Đơn bổ sung",
          backgroundColor: "rgb(255 20 147)",
        },
        {
          label: "Mega",
          backgroundColor: "rgb(180 82 205)",
        }
      ],
    };
  },
  computed: {
    chartDataComputed() {
      var news = {};
      news.labels = this.data_dashboard_pie.labels;
      news.datasets = [
        {
          backgroundColor: this.background_color.map((item) => this.data_dashboard_pie.labels.includes(item.label) ? item.backgroundColor : "rgb(255 255 255)"),
          data: this.data_dashboard_pie.datasets,
        }
      ];
      return news;
    },
  },
};
</script>