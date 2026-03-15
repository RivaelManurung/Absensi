import { apiClient } from "../../../services/apiClient";
import type { ApiDetailResponse, ApiListResponse } from "../../../types/api.types";
import type { ActivityLogRecord } from "../types/activity-log.types";

type ActivityLogListParams = {
  query?: string;
  module?: string;
  action?: string;
  date?: string;
  per_page?: number;
};

function createQueryString(params: ActivityLogListParams) {
  const query = new URLSearchParams();

  query.set("per_page", String(params.per_page ?? 100));

  if (params.query && params.query.trim().length > 0) {
    query.set("query", params.query.trim());
  }

  if (params.module && params.module.trim().length > 0) {
    query.set("module", params.module.trim());
  }

  if (params.action && params.action.trim().length > 0) {
    query.set("action", params.action.trim());
  }

  if (params.date) {
    query.set("date", params.date);
  }

  return query.toString();
}

export const activityLogService = {
  async getActivityLogs(params: ActivityLogListParams = {}): Promise<ActivityLogRecord[]> {
    const queryString = createQueryString(params);
    const response = await apiClient.get<ApiListResponse<ActivityLogRecord>>(`/activity-logs?${queryString}`);
    return response.data.data;
  },

  async getActivityLog(id: string): Promise<ActivityLogRecord> {
    const response = await apiClient.get<ApiDetailResponse<ActivityLogRecord>>(`/activity-logs/${id}`);
    return response.data.data;
  },
};
