import { apiClient } from "../../../services/apiClient";
import type { LoginPayload, LoginResponse, MeResponse } from "../types/auth.types";

export const authService = {
  async login(payload: LoginPayload): Promise<LoginResponse> {
    const response = await apiClient.post<LoginResponse>("/auth/login", payload);
    return response.data;
  },

  async me(): Promise<MeResponse> {
    const response = await apiClient.get<MeResponse>("/auth/me");
    return response.data;
  },

  async logout(): Promise<void> {
    await apiClient.post("/auth/logout");
  },
};
