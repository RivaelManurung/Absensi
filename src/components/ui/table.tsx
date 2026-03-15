import { type PropsWithChildren } from "react";
import { cn } from "../../lib/utils";

export function Table({ children }: PropsWithChildren) {
  return <table className="w-full border-collapse">{children}</table>;
}

export function TableHead({ children }: PropsWithChildren) {
  return <thead className="text-left text-xs uppercase tracking-wider text-muted-foreground">{children}</thead>;
}

export function TableBody({ children }: PropsWithChildren) {
  return <tbody className="divide-y divide-border">{children}</tbody>;
}

export function TableRow({ children, className }: PropsWithChildren<{ className?: string }>) {
  return <tr className={cn("hover:bg-muted/40", className)}>{children}</tr>;
}

export function TableHeaderCell({ children }: PropsWithChildren) {
  return <th className="px-4 py-3 font-medium">{children}</th>;
}

export function TableCell({ children, className }: PropsWithChildren<{ className?: string }>) {
  return <td className={cn("px-4 py-3 text-sm", className)}>{children}</td>;
}
