import { apiClient } from "../../../services/apiClient";
import type { ApiDetailResponse, ApiListResponse } from "../../../types/api.types";
import type { BarcodeRecord } from "../types/barcode.types";

type BarcodeListParams = {
  query?: string;
  status?: "all" | "active" | "inactive";
  employee_id?: string;
  per_page?: number;
};

function createQueryString(params: BarcodeListParams) {
  const query = new URLSearchParams();

  query.set("per_page", String(params.per_page ?? 100));

  if (params.query && params.query.trim().length > 0) {
    query.set("query", params.query.trim());
  }

  if (params.status && params.status !== "all") {
    query.set("status", params.status);
  }

  if (params.employee_id) {
    query.set("employee_id", params.employee_id);
  }

  return query.toString();
}

export const barcodeService = {
  async getBarcodes(params: BarcodeListParams = {}): Promise<BarcodeRecord[]> {
    const queryString = createQueryString(params);
    const response = await apiClient.get<ApiListResponse<BarcodeRecord>>(`/barcodes?${queryString}`);
    return response.data.data;
  },

  async getBarcode(id: string): Promise<BarcodeRecord> {
    const response = await apiClient.get<ApiDetailResponse<BarcodeRecord>>(`/barcodes/${id}`);
    return response.data.data;
  },

  async generate(employeeId: string): Promise<BarcodeRecord> {
    const response = await apiClient.post<ApiDetailResponse<BarcodeRecord>>("/barcodes", { employee_id: employeeId });
    return response.data.data;
  },

  async regenerate(id: string): Promise<void> {
    await apiClient.post(`/barcodes/${id}/regenerate`);
  },

  async deactivate(id: string): Promise<void> {
    await apiClient.post(`/barcodes/${id}/deactivate`);
  },

  async remove(id: string): Promise<void> {
    await apiClient.delete(`/barcodes/${id}`);
  },
};
