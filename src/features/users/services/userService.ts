import { apiClient } from "../../../services/apiClient";
import type { ApiDetailResponse, ApiListResponse } from "../../../types/api.types";
import type { UserListItem, UserListParams, UserRecord } from "../types/user.types";

function createQueryString(params: UserListParams) {
  const query = new URLSearchParams();

  query.set("per_page", String(params.per_page ?? 100));

  if (params.query && params.query.trim().length > 0) {
    query.set("query", params.query.trim());
  }

  if (params.role && params.role !== "all") {
    query.set("role", params.role);
  }

  if (params.status && params.status !== "all") {
    query.set("status", params.status);
  }

  return query.toString();
}

export const userService = {
  async getUsers(params: UserListParams = {}): Promise<UserListItem[]> {
    const queryString = createQueryString(params);
    const usersResponse = await apiClient.get<ApiListResponse<UserRecord>>(`/users?${queryString}`);

    return usersResponse.data.data.map((user) => ({
      id: user.id,
      name: user.name,
      email: user.email,
      role: user.role_name ?? "-",
      status: user.is_active ? "Active" : "Inactive",
      createdDate: user.created_at?.slice(0, 10) ?? "-",
    }));
  },

  async getUser(id: string): Promise<UserRecord> {
    const response = await apiClient.get<ApiDetailResponse<UserRecord>>(`/users/${id}`);
    return response.data.data;
  },

  async createUser(payload: { name: string; email: string; password: string; is_active: boolean }): Promise<void> {
    await apiClient.post("/users", payload);
  },

  async updateUser(
    id: string,
    payload: { name: string; email: string; password?: string; is_active: boolean },
  ): Promise<void> {
    await apiClient.put(`/users/${id}`, payload);
  },

  async deleteUser(id: string): Promise<void> {
    await apiClient.delete(`/users/${id}`);
  },

  async resetPassword(id: string, password: string): Promise<void> {
    await apiClient.post(`/users/${id}/reset-password`, { password });
  },
};
