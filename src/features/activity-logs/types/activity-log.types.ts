export type ActivityLogRecord = {
  id: string;
  user_id?: string | null;
  user_name?: string | null;
  user_email?: string | null;
  action: string;
  module: string;
  description?: string | null;
  ip_address?: string | null;
  user_agent?: string | null;
  created_at: string;
  meta?: Record<string, unknown> | null;
};

export type ActivityLogFilters = {
  query: string;
  module: string;
  action: string;
  date: string;
};
