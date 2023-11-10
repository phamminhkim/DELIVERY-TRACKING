import axios from "axios";
import buildURL from "axios/lib/helpers/buildURL";

class APIRequest {
    constructor(type = 'get', api_url = '/', queries = {}, body = {}) {
        this.type = type;
        this.api_url = api_url;
        this.queries = queries;
        this.body = body;
    }
}
class APIHandler {
    constructor(token) {
        this.token = token;
        this.default_headers =
        {
            "Content-Type": "application/json",
            Authorization: this.token ? `Bearer ${this.token}` : "",
           

        };
        this.custom_headers = {};

    }
    setHeaders(headers) {
        this.custom_headers = headers;
        return this;
    }
    getHeaders() {
        return {
            ...(this.custom_headers ?? this.default_headers),
            Authorization: this.token ? `Bearer ${this.token}` : "",
        };
    }

    async get(api_url, queries = {},response_type='json') {
        try {
            const axiosConfig = {
                headers: this.getHeaders(),
                params:queries,
                responseType: response_type, // Đặt kiểu dữ liệu phản hồi là blob
                };
            const response = await axios.get(api_url,  axiosConfig);
            const data = response.data;
             
            return data;
        } catch (error) {
            console.error("Error fetching data from the API:", error);
            throw error;
        }
        finally {
            this.custom_headers = null;
        }
    }

    async post(api_url, queries = {}, body = {}) {
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
        finally {
            this.custom_headers = null;
        }
    }

    async put(api_url, queries = {}, body = {}) {
        try {
            const response = await axios.put(api_url, body, {
                headers: this.getHeaders(),
                params: queries,
            });
            const data = response.data;

            return data;
        } catch (error) {
            console.error("Error fetching data from the API:", error);
            throw error;
        }
        finally {
            this.custom_headers = null;
        }
    }

    async patch(api_url, queries = {}, body = {}) {
        queries._method = "PATCH";
        try {
            const response = await axios.post(api_url, body, {
                headers: this.getHeaders(),
                params: queries,
            });
            const data = response.data;

            return data;
        } catch (error) {
            console.error("Error fetching data from the API:", error);
            throw error;
        }
        finally {
            this.custom_headers = null;
        }
    }

    async delete(api_url, queries = {}) {
        try {
            const response = await axios.delete(api_url, {
                headers: this.getHeaders(),
                params: queries,
            });
            const data = response.data;

            return data;
        } catch (error) {
            console.error("Error fetching data from the API:", error);
            throw error;
        }
        finally {
            this.custom_headers = null;
        }
    }

    async handleMultipleRequest(requests) {
        try {
            const responses = await Promise.all(
                requests.map(req => {
                    if (req.type == 'get') {
                        return this.get(req.api_url, req.queries);
                    }
                    else if (req.type == 'post') {
                        return this.post(req.api_url, req.queries, req.body);
                    }
                    else if (req.type == 'patch') {
                        return this.patch(req.api_url, req.queries, req.body);
                    }
                    else if (type == 'put') {
                        return this.put(req.api_url, req.queries, req.body);
                    }
                    else if (type == 'delete') {
                        return this.delete(req.api_url, req.queries, req.body);
                    }
                })
            );
            return responses.map(res => res.data);

        }
        catch (err) {
            console.log(err);
        }

    }

    createURL(api_url, queries = {}) {
        return buildURL(api_url, queries);
    }
    static createFormData(datas) {
        let formData = new FormData();
        for (let key in datas) {
            formData.append(key, datas[key]);
        }
        return formData;
    }
}

export {
    APIRequest,
}
export default APIHandler;
