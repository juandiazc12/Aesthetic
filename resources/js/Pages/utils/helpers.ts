export const formatDuration = (duration: number) => {
  const hours = Math.floor(duration / 60);
  const minutes = duration % 60;
  return `${hours > 0 ? `${hours}h ` : ''}${minutes}min`;
};


export const getInitials = (name: string) => {
  const words = name.split(' ');
  const initials = words.map(word => word[0].toUpperCase());
  return initials.slice(0, 2).join('');
};
