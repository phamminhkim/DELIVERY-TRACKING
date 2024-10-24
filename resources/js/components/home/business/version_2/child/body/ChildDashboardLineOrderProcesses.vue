<template>

  <LineChartGenerator :chart-data="chartDatas" :chart-options="chartOptions" :width="width" :height="height" />

</template>

<script>
import { Line as LineChartGenerator } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, LineElement, PointElement, LinearScale, Filler } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, LinearScale, Filler);


export default {
  name: "LineChart",
  components: {
    LineChartGenerator,
  },
  props: {
    width: {
      type: Number,
      default: 100,
    },
    height: {
      type: Number,
      default: 400,
    },
    data_dashboard_line: {
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
          "14-10-2024",
          "15/10/2024",
          "16/10/2024",
          "17/10/2024",
          "18/10/2024",
          "19/10/2024",
          "20/10/2024",
          "21/10/2024",
          "22/10/2024",
          "23/10/2024",
          "24/10/2024",
          "25/10/2024",
          "26/10/2024",
          "27/10/2024",
          "28/10/2024",
          "29/10/2024",
          "30/10/2024",
          "25/10/2024",
          "26/10/2024",
          "27/10/2024",
          "28/10/2024",
          "29/10/2024",
          "30/10/2024",
          "25/10/2024",
          "26/10/2024",
          "27/10/2024",
          "28/10/2024",
          "29/10/2024",
          "30/10/2024",
        ],
        datasets: [
          {
            label: "Thọ, Khanh, Tuyên, Giáp",
            // borderColor: "rgb(191 62 255)", // Màu đường viền
            // borderColor: "rgb(255, 100, 100)", // Màu đường viền
            borderColor: (context) => {
              const chart = context.chart;
              const { ctx, chartArea } = chart;
              if (!chartArea) {
                // This case happens on initial chart load
                return null;
              }
              const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
              gradient.addColorStop(0, 'rgba(255, 100, 100, 1)'); // Đỏ ở trên  
              gradient.addColorStop(0.5, 'rgba(255, 255, 255, 1)'); // Trắng ở giữa  
              gradient.addColorStop(1, 'rgba(255, 0, 0, 1)'); // Đỏ ở dưới  
              return gradient;
            },
            borderWidth: 4, // Độ dày đường viền
            fill: true, // Để đường có phần nền bên dưới
            // pointRadius: 0, // Ẩn các dấu chấm tròn
            tension: 0.09,
            backgroundColor: (context) => {
              const ctx = context.chart.ctx;
              const chartArea = context.chart.chartArea;
              if (!chartArea) {
                return null;
              }
              const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
              // gradient.addColorStop(0, "rgba(191, 62, 255, 0.5)"); // Màu cho vùng dưới đường  
              // gradient.addColorStop(1, "rgba(191, 62, 255, 0)"); // Màu trong suốt cho vùng trên đường  
              gradient.addColorStop(0, "rgba(255, 25, 25, 0.5)"); // Màu cho vùng dưới đường  
              gradient.addColorStop(1, "rgba(255, 0, 0, 0)"); // Màu trong suốt cho vùng trên đường  
              return gradient;
            },
            data: [
              24, 22, 10, 2, 3, 10, 2, 26, 22, 34, 50, 12, 10, 4, 5, 10, 23, 24, 22, 10, 2, 3, 10, 2, 26, 22, 34, 43, 12,
            ], // Số lượng đơn đã đồng bộ của Thọ trong 7 ngày
          },
        ],
      },
      chartOptions: {
        responsive: true,
        // maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
            position: "bottom",
            labels: {
              color: "gray",
              font: {
                size: 14,
                weight: "bold",
              },
            },
          },
          title: {
            display: false,
            text: "PO NGÀY",
            color: "rgb(255 255 224)",
            font: {
              weight: "bold",
            },
          },
          datalabels: {
            display: false, // Hiển thị nhãn
          },
          tooltip: {
            enabled: true,
            backgroundColor: "#227799",
          },
          backgroundColor: "rgb(255 255 224)",
        },
        animations: {
          x: {
            duration: 500,
          },
          y: {
            duration: 500,
          },
        },
        scales: {
          x: {
            grid: {
              // borderColor: "rgb(100 149 237)",
              display: false,
              borderWidth: 2, // Độ dày đường viền
            },
            ticks: {
              minRotation: 90, // Góc xoay nhỏ nhất cho nhãn (90 độ thẳng đứng)
              maxRotation: 90, // Góc xoay lớn nhất cho nhãn
              color: "gray",
              align: "right", // Căn giữa các nhãn
              // Căn giữa nhãn
              offset: true,
              font: {
                size: 12, // Kích thước font
                lineHeight: 1.2, // Chiều cao dòng
              },
            },
          },
          y: {
            grid: {
              display: true, // Tắt lưới cho trục Y
              // borderColor: "rgb(100 149 237)",
              borderWidth: 0.3,
              color: 'gray',
              // đường kẻ nét đứt
              borderDash: [5, 5],
              // độ dài đoạn nét đứt
              borderDashOffset: 5,

            },
            beginAtZero: true, // Vẫn giữ cho trục Y bắt đầu từ 0
            title: {
              display: false,
            },
            ticks: {
              color: "gray",
              font: {
                size: 12, // Kích thước font
                lineHeight: 1.2, // Chiều cao dòng
              },
            },
          },
        },
      },
    };
  },
  methods: {
    backgroundColor(context) {
      const ctx = context.chart.ctx;
      const chartArea = context.chart.chartArea;
      if (!chartArea) {
        return null;
      }
      const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
      gradient.addColorStop(0, "rgba(255, 25, 25, 0.5)"); // Màu cho vùng dưới đường  
      gradient.addColorStop(1, "rgba(255, 0, 0, 0)"); // Màu trong suốt cho vùng trên đường  
      return gradient;
    },
    borderColor(context) {
      const chart = context.chart;
      const { ctx, chartArea } = chart;
      if (!chartArea) {
        // This case happens on initial chart load
        return null;
      }
      const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
      gradient.addColorStop(0, 'rgba(255, 100, 100, 1)'); // Đỏ ở trên  
      gradient.addColorStop(0.5, 'rgba(255, 255, 255, 1)'); // Trắng ở giữa  
      gradient.addColorStop(1, 'rgba(255, 0, 0, 1)'); // Đỏ ở dưới  
      return gradient;
    }
  },
  computed: {
    chartDatas() {
      var news = {};
      news.labels = this.data_dashboard_line.labels;
      news.datasets = [
        {
          // label
          'data': this.data_dashboard_line.datasets,
          borderColor: (context) => this.borderColor(context),
          backgroundColor: (context) => this.backgroundColor(context),
          'borderWidth': 4, // Độ dày đường viền
          'fill': true, // Để đường có phần nền bên dưới
          // pointRadius: 0, // Ẩn các dấu chấm tròn
          'tension': 0.09,
        }
      ]
      return news;
    },
  },
};
</script>
