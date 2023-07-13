import axios from "axios";

class APIHandler {
    constructor(token) {
        this.token = token;
    }
    getHeaders() {
        return {
            "Content-Type": "application/json",
            Authorization: this.token ? `Bearer ${this.token}` : "",
        };
    }

    async get(api_url, queries) {
        try {
            const response = await axios.get(api_url, {
                headers: this.getHeaders(),
                params: queries,
            });
            const data = response.data;

            return data;
        } catch (error) {
            console.error("Error fetching data from the API:", error);
            throw error;
        }
    }
}

export default APIHandler;