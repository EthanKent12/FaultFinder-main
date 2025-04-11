
import React from 'react';
import '../styles/StatusBadge.css';

interface StatusBadgeProps {
  status: "success" | "warning" | "error" | string;
  text: string;
}

const StatusBadge = ({ status, text }: StatusBadgeProps) => {
  return (
    <div className={`status-badge ${status}`}>
      {text}
    </div>
  );
};

export default StatusBadge;