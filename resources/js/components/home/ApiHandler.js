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

    async get(api_url, queries = {}) {
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

    async post(api_url, body = {}, queries = {}) {
        try {
            const response = await axios.post(api_url, body, {
                headers: this.getHeaders(),
                params: queries,
            })
            const data = response.data;

            return data;
        } catch (error) {
            console.error("Error fetching data from the API:", error);
            throw error;
        }
    }

    async put(api_url, body = {}, queries = {}) {
        try {
            const response = await axios.put(api_url, body, {
                headers: this.getHeaders(),
                params: queries,
            })
            const data = response.data;

            return data;
        } catch (error) {
            console.error("Error fetching data from the API:", error);
            throw error;
        }
    }

    async patch(api_url, body = {}, queries = {}) {
        queries._method = "PATCH";
        try {
            const response = await axios.post(api_url, body, {
                headers: this.getHeaders(),
                params: queries,
            })
            const data = response.data;

            return data;
        } catch (error) {
            console.error("Error fetching data from the API:", error);
            throw error;
        }
    }

    async delete(api_url, queries = {}) {
        try {
            const response = await axios.delete(api_url, {
                headers: this.getHeaders(),
                params: queries,
            })
            const data = response.data;

            return data;
        } catch (error) {
            console.error("Error fetching data from the API:", error);
            throw error;
        }
    }
}

export default APIHandler;