import dayjs from "dayjs";
import utc from "dayjs/plugin/utc";
import timezone from "dayjs/plugin/timezone";
import relativeTime from "dayjs/plugin/relativeTime";

dayjs.extend(utc);
dayjs.extend(timezone);
dayjs.extend(relativeTime);

/**
 * Format a UTC date string into local time for display.
 */
export function formatDate(dateString, formatType = "date") {
  if (!dateString) return null;

  const date = dayjs.utc(dateString); // Parse as UTC

  switch (formatType) {
    case "datetime":
      return date.local().format("YYYY-MM-DD hh:mm:ss A");
    case "time":
      return date.local().format("hh:mm A");
    case "short":
      return date.local().format("MMM D");
    case "long":
      return date.local().format("MMMM D, YYYY");
    case "weekday":
      return date.local().format("dddd");
    case "iso":
      return date.toISOString(); // Always UTC
    case "utc_string":
      return new Date(dateString).toUTCString(); // Native UTC string
    case "utc":
      return date.format(); // ISO in UTC
    case "relative":
      return date.fromNow();
    case "date":
    default:
      return date.format("YYYY-MM-DD"); // ⬅️ Core format for date fields
  }
}