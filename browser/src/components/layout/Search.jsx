/* eslint-disable react/button-has-type */
import React from 'react';
import { BiSearch } from 'react-icons/bi';
import Modal from '../ui/Modal.jsx';
import Button from '../ui/Button.jsx';

const API_ENDPOINT = process.env.API_ENDPOINT_SEARCH;

export default function Search() {
    const [searchTerm, setSearchTerm] = React.useState('');

    const resultsInitialState = {
        hits: [],
        isLoading: false,
        isError: false,
    };
    const resultsReducer = (state, action) => {
        switch (action.type) {
            case 'SEARCH_FETCH_INIT':
                return {
                    ...state,
                    isLoading: true,
                    isError: false,
                };
            case 'SEARCH_FETCH_SUCCESS':
                return {
                    ...state,
                    isLoading: false,
                    isError: false,
                    hits: action.payload,
                };
            case 'SEARCH_FETCH_FAIL':
                return {
                    ...state,
                    isLoading: false,
                    isError: true,
                };
            case 'RESET':
                return {
                    ...state,
                    isLoading: false,
                    isError: false,
                    hits: [],
                };
            default:
                throw new Error();
        }
    };
    // call `dispatchResults` to change `results` object
    const [results, dispatchResults] = React.useReducer(resultsReducer, resultsInitialState);

    const handleSearch = (event) => {
        setSearchTerm(event.target.value);
    };

    React.useEffect(() => {
        if (!searchTerm) {
            dispatchResults({ type: 'RESET' });
            return;
        }

        if (searchTerm.length < 3) {
            return;
        }

        // Mark search form as initializing/loading
        dispatchResults({ type: 'SEARCH_FETCH_INIT' });

        // Fetch from API
        const url = API_ENDPOINT + searchTerm;
        fetch(url)
            .then((response) => response.json())
            .then((result) => {
                dispatchResults({
                    type: 'SEARCH_FETCH_SUCCESS',
                    payload: result.collection.items,
                });
            })
            .catch(() => dispatchResults({ type: 'SEARCH_FETCH_FAIL' }));
    }, [searchTerm]);

    const [open, setOpen] = React.useState(false);
    const handleClose = () => {
        setOpen(false);
    };

    return (
        <div id="search">
            <Button classes={{ 'button-header': true }} onClick={() => setOpen(true)}>
                <BiSearch size="28" title="Search" />
            </Button>
            <Modal open={open} close={handleClose} closeTabIndex="0">
                <div>
                    <input id="searchform" type="text" value={searchTerm} placeholder="Search all the things" tabIndex="0" onChange={handleSearch} ref={(input) => input && input.focus()} />
                    {' '}
                    <button type="reset" tabIndex="0" onClick={() => setSearchTerm('')}>Reset</button>

                    {results.isError && <p>Something went wrong</p>}

                    {results.isLoading ? (<p>Loading...</p>) : (<SearchResults results={results} />)}
                </div>
            </Modal>
        </div>
    );
}

function SearchResults(props) {
    const { results } = props;

    if (results.hits.length === 0) {
        return <p>No results found</p>;
    }

    return (
        <ul>
            {results.hits.map((item) => <SearchResult key={item.title_sort} item={item} />)}
        </ul>
    );
}

/**
 * Item component
 * @param {Object} props.item Item object
 * @param {} onRemoveItem
 */
function SearchResult(props) {
    const { item } = props;

    return (
        <li>
            <a href={item.links.page}>
                <dfn>{item.title}</dfn>
                {' '}
                <span>
                    (
                    {item.type}
                    )
                </span>
            </a>
        </li>
    );
}

// ReactDOM.render(
//     React.createElement(Search),
//     document.getElementById('search'),
// );
