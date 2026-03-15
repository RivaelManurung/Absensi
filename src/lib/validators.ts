export function isValidEmail(value: string): boolean {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
}

export function hasMinLength(value: string, min = 6): boolean {
  return value.trim().length >= min;
}
