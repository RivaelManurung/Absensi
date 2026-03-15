export type ApiResponse<T> = {
  data: T;
  message?: string;
};

export type AppTheme = "dark" | "light";
