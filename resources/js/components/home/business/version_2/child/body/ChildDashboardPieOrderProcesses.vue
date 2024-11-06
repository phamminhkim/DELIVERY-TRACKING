<template>
  <div v-if="chartDataComputed">
    <Pie :chart-options="chartOptions" :chart-data="chartDataComputed" :chart-id="chartId"
      :dataset-id-key="datasetIdKey" />
  </div>
  <div v-else class="none-data">
    Không có dữ liệu để hiển thị
  </div>

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
            display: true,
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
            // color: 'black', // Màu văn bản  
            color: (context) => {
              const backgroundColor = context.dataset.backgroundColor[context.dataIndex];
              return this.getTextColor(backgroundColor); // Hàm xác định màu chữ
            },
            font: {
              size: 12, // Kích thước font
              lineHeight: 1.2, // Chiều cao dòng
              weight: 'bold',
            },
          },
        },
      },
      background_color: [],
    };
  },
  methods: {
    getTextColor(backgroundColor) {
      const [r, g, b] = backgroundColor.match(/\d+/g).map(Number);
      const brightness = (r * 299 + g * 587 + b * 114) / 1000;
      return brightness < 128 ? 'white' : 'black';
    },
    generateRandomColor() {
      let color;
      do {
        // Sinh màu ngẫu nhiên nhưng thể hiện được màu rõ ràng và khác biệt không sử dụng màu quá giông nhau
        const r = Math.floor(Math.random() * 256);
        const g = Math.floor(Math.random() * 256);
        const b = Math.floor(Math.random() * 256);
        
       
        color = `rgb(${r}, ${g}, ${b})`;
      } while (this.background_color.includes(color)); // Lặp lại nếu màu đã tồn tại

      // Lưu màu mới vào mảng background_color
      this.background_color.push(color);
      return color;
    },
  },
  computed: {
    chartDataComputed() {
      const data = this.data_dashboard_pie.datasets;

      // Kiểm tra nếu tất cả giá trị đều bằng 0
      const allZero = data.every(value => value === 0);

      if (allZero) {
        return null; // Hoặc có thể return một cấu trúc đặc biệt để hiển thị thông báo
      }

      return {
        labels: this.data_dashboard_pie.labels,
        datasets: [
          {
            backgroundColor: this.data_dashboard_pie.labels.map(() => this.generateRandomColor()),
            data: data,
          }
        ]
      };
    },
  },
};
</script>
<style lang="scss" scoped>
.none-data {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}
</style>