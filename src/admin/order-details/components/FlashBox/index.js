export const FlashBox = ({ className = 'fo-success', children, index, onClose }) => {
    return (
        <div key={index} className={`fo-flash-message ${className}`}>
            <div className="fo-icon">
                {
                    className === 'fo-error' ?
                    (
                        <svg
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            strokeWidth="2"
                            aria-hidden="true"
                            className="error-x-icon"
                        >
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    )
                    : className === 'fo-warning' ?
                    (
                        <svg
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            strokeWidth="2"
                            aria-hidden="true"
                            className="warning-icon"
                        >
                            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    )
                    : 
                    (
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                            <path d="M20 6L9 17l-5-5" />
                        </svg>
                    )
                }
            </div>
            <div className="fo-content">
                <div className="fo-flash-description">{children}</div>
            </div>
            <button onClick={onClose} className="fo-close">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                    <path d="M18 6L6 18M6 6l12 12" />
                </svg>
            </button>
        </div>
    );
};
