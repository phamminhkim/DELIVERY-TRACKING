<template>
  <BarCustom :chart-options="chartOptions" :chart-data="chartDataComputed" :width="width" :height="height" />
</template>

<script>
// import { Bar } from "vue-chartjs/legacy";
import { Bar as BarCustom } from 'vue-chartjs';
import { Chart as ChartJSBar, Title, LinearScale, CategoryScale } from 'chart.js';
import ChartDataLabels from 'chartjs-plugin-datalabels'; // Import plugin data labels 

ChartJSBar.register(Title, LinearScale, CategoryScale, ChartDataLabels); // Register plugin  



export default {

  components: {
    BarCustom,
  },
  props: {

    width: {
      type: Number,
      default: 100,
    },
    height: {
      type: Number,
      default: 100,
    },
    data_dashboard_user: {
      type: Object,
      default: () => {
        return {
          users: [],
          unsynced_orders: [],
          total_orders: [],
          synced_orders: []
        };
      },
    },
  },
  data() {
    return {
      chartData: {
        labels: ["Khanh", "Tuyen", "Giap", "Tho", "Sang"],
        datasets: [
          {
            label: "Đã đồng bộ",
            // backgroundColor: "rgb(124 252 0)", // Màu highlight cho phần chưa đồng bộ
            backgroundColor: "#28a745",
            data: [10, 15, 20, 10, 40], // Đơn chưa đồng bộ cho mỗi user
            barPercentage: 0.8,
            barThickness: 20,
            maxBarThickness: 20,
            minBarLength: 2,

          },
          {
            label: "Chưa đồng bộ",
            // backgroundColor: "rgb(255 255 224)", // Màu cho phần đã đồng bộ
            // backgroundColor: "#ffc107",
            backgroundColor: "white",
            data: [5, 10, 15, 35, 20], // Đơn đã đồng bộ cho mỗi user
            barPercentage: 0.5,
            barThickness: 20,
            maxBarThickness: 20,
            minBarLength: 2,
          },
        ],
      },
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: "y", // Chuyển trục để tạo biểu đồ cột ngang
        plugins: {
          tooltip: {
            enabled: true,
            backgroundColor: "#227799",
          },
          datalabels: {
            anchor: 'center', // Vị trí của nhãn (dưới cùng cột)  
            align: 'center', // Căn chỉnh nhãn (dưới cùng cột)  
            color: 'black', // Màu văn bản  
            font: {
              weight: 'bold', // Độ đậm của văn bản  
            },
          },
          title: {
            display: true,
            text: "PO NGƯỜI DÙNG",
            color: "rgb(255 255 224)",
            // color: "black",
            font: {
              weight: "bold",
            },
          },
          legend: {
            display: true,
            position: "right",
            labels: {
              color: "rgb(255 255 224)",
              font: {
                weight: "bold",
              },
            },
          },
        },
        scales: {
          x: {
            beginAtZero: true,
            stacked: true, // Bật chế độ xếp chồng cho trục X (hoặc Y nếu là biểu đồ ngang)
            ticks: {
              minor: {
                enabled: true, // show minor ticks
                display: "auto", // display minor ticks automatically
                color: "gray", // color of minor ticks
                lineWidth: 0.5, // width of minor ticks
              },
            },
            grid: {
              drawTicks: true,
              display: true,
              offset: false,
              tickColor: "#707070",
              color: "transparent",
            },
            border: {
              display: true,
              color: "#707070",
            },
            ticks: { color: "white" },
          },
          y: {
            beginAtZero: true,
            stacked: true, // Bật chế độ xếp chồng cho trục Y
            ticks: {
              minor: {
                enabled: true, // show minor ticks
                display: "auto", // display minor ticks automatically
                color: "white", // color of minor ticks
                lineWidth: 0.5, // width of minor ticks
              },
            },
            grid: {
              drawTicks: true,
              display: true,
              offset: false,
              tickColor: "#707070",
              color: "transparent",
            },
            border: {
              display: true,
              color: "#707070",
            },
            ticks: { color: "white" },
          },
        },
      },
    };
  },
  computed: {
    chartDataComputed() {
      // return this.data_dashboard_user;
      var news = {};
      news.labels = this.data_dashboard_user.users;
      news.datasets = [
        {
          label: "Đã đồng bộ",
          backgroundColor: "rgb(124 252 0)", // Màu highlight cho phần chưa đồng bộ
          data: this.data_dashboard_user.synced_orders, // Đơn chưa đồng bộ cho mỗi user
          barPercentage: 0.8,
          barThickness: 20,
          maxBarThickness: 20,
          minBarLength: 2,
        },
        {
          label: "Chưa đồng bộ",
          backgroundColor: "rgb(255 255 224)", // Màu cho phần đã đồng bộ
          data: this.data_dashboard_user.unsynced_orders, // Đơn đã đồng bộ cho mỗi user
          barPercentage: 0.5,
          barThickness: 20,
          maxBarThickness: 20,
          minBarLength: 2,
        }
      ]
      return news;
    },
  },
};
</script>
