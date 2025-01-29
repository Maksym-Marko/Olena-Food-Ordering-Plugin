import { useState } from 'react';
import { __ } from '@wordpress/i18n';
import { useNavigate } from 'react-router-dom';
import { useDispatch, useSelector } from "react-redux"
import { setCurrentPage } from "@olenaStore/store/slices/menu/menuSlice"

export const Pagination = ({ totalPages }) => {

    const currentPage = useSelector(state => state.menu.currentPage)
    const perPage = useSelector(state => state.menu.perPage)

    const dispatch = useDispatch();
    const navigate = useNavigate();

    const handlePageChange = (newPage) => {
        if (newPage >= 1 && newPage <= totalPages) {

            dispatch(setCurrentPage(newPage));

            navigate(`/page/${newPage}`);

            setTimeout(() => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }, 200);
        }
    };

    const renderPageNumbers = () => {
        const pages = [];
        const showEllipsis = totalPages > 7;

        if (!showEllipsis) {
            // Show all pages if total pages are 7 or less
            for (let i = 1; i <= totalPages; i++) {
                pages.push(i);
            }
        } else {
            // Show pages with ellipsis
            if (currentPage <= 3) {
                // Current page is near the start
                for (let i = 1; i <= 4; i++) pages.push(i);
                pages.push('...');
                pages.push(totalPages);
            } else if (currentPage >= totalPages - 2) {
                // Current page is near the end
                pages.push(1);
                pages.push('...');
                for (let i = totalPages - 3; i <= totalPages; i++) pages.push(i);
            } else {
                // Current page is in the middle
                pages.push(1);
                pages.push('...');
                pages.push(currentPage - 1);
                pages.push(currentPage);
                pages.push(currentPage + 1);
                pages.push('...');
                pages.push(totalPages);
            }
        }

        return pages;
    };

    return (
        <div className="ofo-main-menu-pagination">
            <button
                onClick={() => handlePageChange(currentPage - 1)}
                disabled={currentPage === 1}
                className="ofo-main-menu-previous-page"
                aria-label="Previous page"
            >
                {__('Prev', 'olena-food-ordering')}
            </button>

            {renderPageNumbers().map((page, index) => (
                <button
                    key={index}
                    onClick={() => page !== '...' && handlePageChange(page)}
                    disabled={page === '...'}
                    className={`ofo-menu-page ${page === currentPage
                            ? 'ofo-current-page'
                            : ''
                        } ${page === '...' ? 'ofo-menu-page-cursor-default' : ''}`}
                >
                    {page}
                </button>
            ))}

            <button
                onClick={() => handlePageChange(currentPage + 1)}
                disabled={currentPage === totalPages}
                className="ofo-main-menu-next-page"
                aria-label="Next page"
            >
                {__('Next', 'olena-food-ordering')}
            </button>
        </div>
    );
};